<?php declare(strict_types = 1);

namespace WebChemistry\Type\Single;

use WebChemistry\Type\DataType;

final class IterableDataType extends SingleDataTypeAbstract
{

	public function allows(DataType $type): bool
	{
		return parent::allows($type) || $type instanceof ArrayDataType;
	}

}
