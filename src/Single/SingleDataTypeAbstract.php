<?php declare(strict_types = 1);

namespace WebChemistry\Type\Single;

use WebChemistry\Type\DataType;
use WebChemistry\Type\SingleDataType;

abstract class SingleDataTypeAbstract implements SingleDataType
{

	final public function __construct(
		protected string $type,
	)
	{
	}

	public function isBuiltin(): bool
	{
		return true;
	}

	public function isSingle(): bool
	{
		return true;
	}

	public function equalTo(DataType $type): bool
	{
		if (!$type->isSingle()) {
			return false;
		}

		return $type->toString() === $this->type;
	}

	public function allows(DataType $type): bool
	{
		return $type->toString() === $this->type;
	}

	/**
	 * @return string[]
	 */
	public function getNames(): array
	{
		return [$this->type];
	}

	/**
	 * @return SingleDataTypeAbstract[]
	 */
	public function getTypes(): array
	{
		return [$this];
	}

	/**
	 * @return SingleDataTypeAbstract[]
	 */
	public function getSingleTypes(): array
	{
		return [$this];
	}

	public function toString(): string
	{
		return $this->type;
	}

}
