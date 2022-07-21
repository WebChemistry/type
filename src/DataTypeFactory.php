<?php declare(strict_types = 1);

namespace WebChemistry\Type;

interface DataTypeFactory
{

	public function createFromString(string $type): DataType;

}
