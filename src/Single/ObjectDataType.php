<?php declare(strict_types = 1);

namespace WebChemistry\Type\Single;

use WebChemistry\Type\DataType;

final class ObjectDataType extends SingleDataTypeAbstract
{

	public function allowsType(DataType $type): bool
	{
		return !$type->isBuiltin() || $type instanceof ObjectDataType;
	}

}
