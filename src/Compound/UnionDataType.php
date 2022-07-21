<?php declare(strict_types = 1);

namespace WebChemistry\Type\Compound;

use Nette\Utils\Arrays;
use WebChemistry\Type\CompoundDataType;
use WebChemistry\Type\DataType;
use WebChemistry\Type\Single\SingleDataTypeAbstract;

final class UnionDataType implements CompoundDataType
{

	/**
	 * @param SingleDataTypeAbstract[] $types
	 */
	public function __construct(
		private array $types,
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

	public function equalTo(DataType $type): bool
	{
		if ($type->isSingle()) {
			return false;
		}

		if (count($type->getTypes()) !== count($this->types)) {
			return false;
		}

		return $this->allows($type);
	}

	public function allows(DataType $type): bool
	{
		if ($type->isSingle() && $type->toString() === 'mixed') {
			return false;
		}

		return Arrays::every(
			$type->getSingleTypes(),
			fn (DataType $testedType) => Arrays::some(
				$this->types,
				fn (DataType $currentType) => $currentType->allows($testedType),
			)
		);
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
