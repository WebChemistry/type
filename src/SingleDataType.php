<?php declare(strict_types = 1);

namespace WebChemistry\Type;

final class SingleDataType implements DataType
{

	private const BUILT_IN = [
		'array' => true,
		'callable' => true,
		'bool' => true,
		'true' => true,
		'false' => true,
		'float' => true,
		'int' => true,
		'string' => true,
		'iterable' => true,
		'object' => true,
		'mixed' => true,
		'never' => true,
		'null' => true,
	];

	private bool $builtin = false;

	private bool $mixed = false;

	public function __construct(
		private string $type,
	)
	{
		$this->type = ltrim(trim($this->type), '\\');
		$lowerType = strtolower($this->type);

		if (isset(self::BUILT_IN[$lowerType])) {
			$this->builtin = true;
			$this->type = $lowerType;
			$this->mixed = $this->type === 'mixed';
		}
	}

	public function isBuiltin(): bool
	{
		return $this->builtin;
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
		if ($this->mixed) {
			return true;
		}

		if ($type->isSingle()) {
			if ($this->builtin) {
				return $type->isBuiltin() && $type->toString() === $this->type;
			}

			return $type->toString() === $this->type || is_a($this->type, $type->toString(), true);
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
	 * @return SingleDataType[]
	 */
	public function getTypes(): array
	{
		return [$this];
	}

	/**
	 * @return SingleDataType[]
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
