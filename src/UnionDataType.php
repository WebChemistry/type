<?php declare(strict_types = 1);

namespace WebChemistry\Type;

use Nette\Utils\Arrays;

final class UnionDataType implements DataType
{

	/**
	 * @param DataType[] $types
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
			$type->getTypes(),
			fn (DataType $testedType) => Arrays::some(
				$this->types,
				fn (DataType $currentType) => $testedType->allows($currentType),
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
	 * @return DataType[]
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
