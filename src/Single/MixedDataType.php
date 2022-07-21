<?php declare(strict_types = 1);

namespace WebChemistry\Type\Single;

use WebChemistry\Type\DataType;

final class MixedDataType extends SingleDataTypeAbstract
{

	public function allows(DataType $type): bool
	{
		return true;
	}

}
