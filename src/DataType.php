<?php declare(strict_types = 1);

namespace WebChemistry\Type;

use WebChemistry\Type\Single\SingleDataTypeAbstract;

interface DataType
{

	/**
	 * @return string[]
	 */
	public function getNames(): array;

	public function allows(DataType $type): bool;

	public function equalTo(DataType $type): bool;

	/**
	 * @return DataType[]
	 */
	public function getTypes(): array;

	/**
	 * @return SingleDataTypeAbstract[]
	 */
	public function getSingleTypes(): array;

	public function isBuiltin(): bool;

	public function isSingle(): bool;

	public function toString(): string;

}
