<?php declare(strict_types = 1);

namespace WebChemistry\Type\Single;

use WebChemistry\Type\DataType;
use WebChemistry\Type\DataTypeFactory;
use WebChemistry\Type\SingleDataType;

abstract class SingleDataTypeAbstract implements SingleDataType
{

	final public function __construct(
		protected string $type,
		protected DataTypeFactory $dataTypeFactory,
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

	public function equalTo(string $type): bool
	{
		return $this->equalToType($this->dataTypeFactory->createFromString($type));
	}

	public function equalToType(DataType $type): bool
	{
		if (!$type->isSingle()) {
			return false;
		}

		return $type->toString() === $this->type;
	}

	public function allows(string $type): bool
	{
		return $this->allowsType($this->dataTypeFactory->createFromString($type));
	}

	public function allowsType(DataType $type): bool
	{
		return $type->toString() === $this->type;
	}

	public function allowsAny(string ...$types): bool
	{
		return $this->allowsAnyTypes(...array_map($this->dataTypeFactory->createFromString(...), $types));
	}

	public function allowsAnyTypes(DataType ...$types): bool
	{
		foreach ($types as $type) {
			if ($this->allowsType($type)) {
				return true;
			}
		}

		return false;
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
