<?php declare(strict_types = 1);

namespace WebChemistry\Type\Helper;

use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionParameter;
use ReflectionProperty;

final class DataTypeHelper
{

	public const BUILTIN_VALUES = [
		'array',
		'callable',
		'bool',
		'true',
		'false',
		'float',
		'int',
		'string',
		'iterable',
		'object',
		'mixed',
		'never',
		'null',
		'resource',
	];

	private const BUILTIN = [
		'array' => true,
		'callable' => true,
		'bool' => true,
		'true' => true,
		'false' => true,
		'float' => true,
		'int' => true,
		'string' => true,
		'iterable' => true,
		'object' => true,
		'mixed' => true,
		'never' => true,
		'null' => true,
		'resource' => true,
	];

	public static function resolve(
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

	public static function isBuiltin(string $type): bool
	{
		return isset(self::BUILTIN[strtolower($type)]);
	}

}
