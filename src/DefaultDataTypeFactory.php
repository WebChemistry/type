<?php declare(strict_types = 1);

namespace WebChemistry\Type;

final class DefaultDataTypeFactory implements DataTypeFactory
{

	public function createFromString(string $type): DataType
	{
		return StaticDataTypeFactory::fromString($type);
	}

}
