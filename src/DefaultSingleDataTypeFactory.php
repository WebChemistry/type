<?php declare(strict_types = 1);

namespace WebChemistry\Type;

use WebChemistry\Type\Single\ArrayDataType;
use WebChemistry\Type\Single\BoolDataType;
use WebChemistry\Type\Single\CallableDataType;
use WebChemistry\Type\Single\ClassDataType;
use WebChemistry\Type\Single\FalseDataType;
use WebChemistry\Type\Single\FloatDataType;
use WebChemistry\Type\Single\IntDataType;
use WebChemistry\Type\Single\IterableDataType;
use WebChemistry\Type\Single\MixedDataType;
use WebChemistry\Type\Single\NeverDataType;
use WebChemistry\Type\Single\NullDataType;
use WebChemistry\Type\Single\ObjectDataType;
use WebChemistry\Type\Single\ResourceDataType;
use WebChemistry\Type\Single\SingleDataTypeAbstract;
use WebChemistry\Type\Single\StringDataType;
use WebChemistry\Type\Single\TrueDataType;

final class DefaultSingleDataTypeFactory
{

	private const BUILTIN = [
		'array' => ArrayDataType::class,
		'callable' => CallableDataType::class,
		'bool' => BoolDataType::class,
		'true' => TrueDataType::class,
		'false' => FalseDataType::class,
		'float' => FloatDataType::class,
		'int' => IntDataType::class,
		'string' => StringDataType::class,
		'iterable' => IterableDataType::class,
		'object' => ObjectDataType::class,
		'mixed' => MixedDataType::class,
		'never' => NeverDataType::class,
		'null' => NullDataType::class,
		'resource' => ResourceDataType::class,
	];

	public function __construct(
		private DataTypeFactory $dataTypeFactory,
	)
	{
	}

	public function create(string $type): SingleDataTypeAbstract
	{
		$type = ltrim(trim($type), '\\');
		$lowerType = strtolower($type);

		if (isset(self::BUILTIN[$lowerType])) {
			return new (self::BUILTIN[$lowerType])($lowerType, $this->dataTypeFactory);
		}

		return new ClassDataType($type, $this->dataTypeFactory);
	}

}
