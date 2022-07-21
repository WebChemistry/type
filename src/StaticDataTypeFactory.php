<?php declare(strict_types = 1);

namespace WebChemistry\Type;

use InvalidArgumentException;
use LogicException;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionIntersectionType;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionProperty;
use ReflectionUnionType;
use WebChemistry\Type\Compound\UnionDataType;
use WebChemistry\Type\StaticSingleDataTypeFactory;

final class StaticDataTypeFactory
{

	public static function fromString(string $type): DataType
	{
		if (str_contains($type, '&')) {
			throw new LogicException('Intersections are not currently supported.');
		}

		if (str_starts_with($type, '?')) {
			$type = substr($type, 1) . '|null';
		}

		$types = array_map(
			fn (string $type) => StaticSingleDataTypeFactory::create($type),
			explode('|', $type),
		);

		return count($types) > 1 ? new UnionDataType($types) : $types[0];
	}

	public static function fromReflection(
		ReflectionFunctionAbstract|ReflectionParameter|ReflectionProperty $reflection,
	): ?DataType
	{
		if ($reflection instanceof ReflectionMethod) {
			$type = $reflection->getReturnType() ?? (PHP_VERSION_ID >= 80100 ? $reflection->getTentativeReturnType() : null);
		} else {
			$type = $reflection instanceof ReflectionFunctionAbstract
				? $reflection->getReturnType()
				: $reflection->getType();
		}

		if ($type === null) {
			return null;

		} elseif ($type instanceof ReflectionNamedType) {
			$name = self::resolve($type->getName(), $reflection);

			if ($type->allowsNull() && $type->getName() !== 'mixed') {
				return new UnionDataType([
					StaticSingleDataTypeFactory::create($name),
					StaticSingleDataTypeFactory::create('null')
				]);
			}

			return StaticSingleDataTypeFactory::create($name);

		} elseif ($type instanceof ReflectionUnionType) {
			$types = array_map(
				fn (ReflectionNamedType $t) => StaticSingleDataTypeFactory::create(
					self::resolve($t->getName(), $reflection)
				),
				$type->getTypes()
			);

			return new UnionDataType($types);

		} elseif ($type instanceof ReflectionIntersectionType) {
			throw new LogicException(sprintf('Intersections are not currently supported.'));

		} else {
			throw new InvalidArgumentException('Unexpected type of ' . get_debug_type($reflection));
		}
	}

	private static function resolve(
		string $type,
		ReflectionFunctionAbstract|ReflectionParameter|ReflectionProperty $reflection,
	): string
	{
		$lower = strtolower($type);

		if ($reflection instanceof ReflectionFunction) {
			return $type;
		} elseif ($lower === 'self' || $lower === 'static') {
			return $reflection->getDeclaringClass()->name;
		} elseif ($lower === 'parent' && $reflection->getDeclaringClass()->getParentClass()) {
			return $reflection->getDeclaringClass()->getParentClass()->name;
		} else {
			return $type;
		}
	}

}
