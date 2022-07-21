<?php declare(strict_types = 1);

namespace WebChemistry\Type;

use ReflectionFunctionAbstract;
use ReflectionParameter;
use ReflectionProperty;

interface DataTypeFactory
{

	public function createFromString(string $type): DataType;

	public function createFromReflection(
		ReflectionFunctionAbstract|ReflectionParameter|ReflectionProperty $reflection,
	): ?DataType;

}
