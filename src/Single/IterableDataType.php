<?php declare(strict_types = 1);

namespace WebChemistry\Type\Single;

use WebChemistry\Type\DataType;

final class IterableDataType extends SingleDataTypeAbstract
{

	public function allowsType(DataType $type): bool
	{
		return parent::allowsType($type) || $type instanceof ArrayDataType;
	}

}
