<?php declare(strict_types = 1);

namespace WebChemistry\Type\Simple;

use InvalidArgumentException;
use ReflectionFunctionAbstract;
use ReflectionIntersectionType;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionProperty;
use ReflectionUnionType;
use WebChemistry\Type\Helper\DataTypeHelper;

final class SimpleDataTypeFactory
{

	public static function fromString(string $type): ?SimpleDataType
	{
		$nullable = false;
		if (str_starts_with($type, '?')) {
			$nullable = true;

			if (str_contains($type, '|')) {
				return null;
			}

			$type = substr($type, 1);

		} elseif (($count = count($explode = explode('|', $type))) > 1) {
			if ($count > 2) {
				return null;
			}

			if (strcasecmp($explode[0], 'null') === 0) {
				$nullable = true;
				$type = $explode[1];

			} elseif (strcasecmp($explode[1], 'null') === 0) {
				$nullable = true;
				$type = $explode[0];

			} else {
				return null;

			}
		} elseif (str_contains($type, '&')) {
			return null;
		}

		return new SimpleDataType($type, $nullable);
	}

	public static function fromReflection(
		ReflectionFunctionAbstract|ReflectionParameter|ReflectionProperty $reflection,
	): ?SimpleDataType
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

			if ($type->allowsNull() && $name !== 'mixed') {
				return new SimpleDataType($name, true);
			}

			return new SimpleDataType($name);

		} elseif ($type instanceof ReflectionUnionType) {
			return null;

		} elseif ($type instanceof ReflectionIntersectionType) {
			return null;

		} else {
			throw new InvalidArgumentException('Unexpected type of ' . get_debug_type($reflection));
		}
	}

}
