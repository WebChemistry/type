<?php declare(strict_types = 1);

namespace WebChemistry\Type;

use WebChemistry\Type\Single\SingleDataTypeAbstract;

interface DataType
{

	/**
	 * @return string[]
	 */
	public function getNames(): array;

	public function allows(string $type): bool;

	public function allowsAny(string ... $types): bool;

	public function allowsType(DataType $type): bool;

	public function allowsAnyTypes(DataType ... $types): bool;

	public function equalTo(string $type): bool;

	public function equalToType(DataType $type): bool;

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
