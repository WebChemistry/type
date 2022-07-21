<?php declare(strict_types = 1);

namespace WebChemistry\Type;

use ReflectionFunctionAbstract;
use ReflectionParameter;
use ReflectionProperty;

final class DefaultDataTypeFactory implements DataTypeFactory
{

	public function createFromString(string $type): DataType
	{
		return StaticDataTypeFactory::fromString($type);
	}

	public function createFromReflection(
		ReflectionFunctionAbstract|ReflectionParameter|ReflectionProperty $reflection,
	): DataType
	{
		return StaticDataTypeFactory::fromReflection($reflection);
	}

}
