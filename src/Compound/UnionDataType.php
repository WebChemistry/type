<?php declare(strict_types = 1);

namespace WebChemistry\Type\Compound;

use Nette\Utils\Arrays;
use WebChemistry\Type\CompoundDataType;
use WebChemistry\Type\DataType;
use WebChemistry\Type\DataTypeFactory;
use WebChemistry\Type\Single\SingleDataTypeAbstract;

final class UnionDataType implements CompoundDataType
{

	/**
	 * @param SingleDataTypeAbstract[] $types
	 */
	public function __construct(
		private array $types,
		private DataTypeFactory $dataTypeFactory,
	)
	{
	}

	public function isBuiltin(): bool
	{
		return false;
	}

	public function isSingle(): bool
	{
		return false;
	}

	/**
	 * @return string[]
	 */
	public function getNames(): array
	{
		$names = [];
		foreach ($this->types as $type) {
			foreach ($type->getNames() as $name) {
				$names[] = $name;
			}
		}

		return $names;
	}

	public function equalTo(string $type): bool
	{
		return $this->equalToType($this->dataTypeFactory->createFromString($type));
	}

	public function equalToType(DataType $type): bool
	{
		if ($type->isSingle()) {
			return false;
		}

		if (count($type->getTypes()) !== count($this->types)) {
			return false;
		}

		return $this->allowsType($type);
	}

	public function allows(string $type): bool
	{
		return $this->allowsType($this->dataTypeFactory->createFromString($type));
	}

	public function allowsType(DataType $type): bool
	{
		if ($type->isSingle() && $type->toString() === 'mixed') {
			return false;
		}

		return Arrays::every(
			$type->getSingleTypes(),
			fn (DataType $testedType) => Arrays::some(
				$this->types,
				fn (DataType $currentType) => $currentType->allowsType($testedType),
			)
		);
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
	 * @return DataType[]
	 */
	public function getTypes(): array
	{
		return $this->types;
	}

	/**
	 * @return SingleDataTypeAbstract[]
	 */
	public function getSingleTypes(): array
	{
		return $this->types;
	}

	public function toString(): string
	{
		return implode('|', array_map(
			fn (DataType $type) => $type->toString(),
			$this->types,
		));
	}

}
