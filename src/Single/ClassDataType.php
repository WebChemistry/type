<?php declare(strict_types = 1);

namespace WebChemistry\Type\Single;

use WebChemistry\Type\DataType;

/**
 * @method class-string toString()
 */
final class ClassDataType extends SingleDataTypeAbstract
{

	public function isBuiltin(): bool
	{
		return false;
	}

	public function allows(DataType $type): bool
	{
		if ($type->isBuiltin()) {
			return false;
		}

		return $type->toString() === $this->type || is_a($type->toString(), $this->type, true);
	}

}
