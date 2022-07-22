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
use WebChemistry\Type\Helper\DataTypeHelper;

final class StaticDataTypeFactory
{

	private static DataTypeFactory $factory;

	private static DefaultSingleDataTypeFactory $singleTypeFactory;

	public static function fromString(string $type): DataType
	{
		if (str_contains($type, '&')) {
			throw new LogicException('Intersections are not currently supported.');
		}

		if (str_starts_with($type, '?')) {
			$type = substr($type, 1) . '|null';
		}

		$types = array_map(
			fn (string $type) => self::getSingleTypeFactory()->create($type),
			explode('|', $type),
		);

		return count($types) > 1 ? new UnionDataType($types, self::getFactory()) : $types[0];
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
			$name = DataTypeHelper::resolve($type->getName(), $reflection);

			if ($type->allowsNull() && $type->getName() !== 'mixed') {
				return new UnionDataType([
					self::getSingleTypeFactory()->create($name),
					self::getSingleTypeFactory()->create('null'),
				], self::getFactory());
			}

			return self::getSingleTypeFactory()->create($name);

		} elseif ($type instanceof ReflectionUnionType) {
			$types = array_map(
				fn (ReflectionNamedType $t) => self::getSingleTypeFactory()->create(
					DataTypeHelper::resolve($t->getName(), $reflection)
				),
				$type->getTypes()
			);

			return new UnionDataType($types, self::getFactory());

		} elseif ($type instanceof ReflectionIntersectionType) {
			throw new LogicException(sprintf('Intersections are not currently supported.'));

		} else {
			throw new InvalidArgumentException('Unexpected type of ' . get_debug_type($reflection));
		}
	}

	private static function getSingleTypeFactory(): DefaultSingleDataTypeFactory
	{
		return self::$singleTypeFactory ??= new DefaultSingleDataTypeFactory(self::getFactory());
	}

	private static function getFactory(): DataTypeFactory
	{
		return self::$factory ??= new DefaultDataTypeFactory();
	}

}
