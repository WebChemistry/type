<?php declare(strict_types = 1);

namespace WebChemistry\Type\Simple;

use JetBrains\PhpStorm\ExpectedValues;
use WebChemistry\Type\Helper\DataTypeHelper;

final class SimpleDataType
{

	private bool $builtin = false;

	public function __construct(
		private string $type,
		private bool $nullable = false,
	)
	{
		if (DataTypeHelper::isBuiltin($this->type)) {
			$this->type = strtolower($this->type);
			$this->builtin = true;
		}
	}

	public function is(
		#[ExpectedValues(DataTypeHelper::BUILTIN_VALUES)]
		string $type,
	): bool
	{
		if ($this->builtin) {
			if (strcasecmp($this->type, $type) === 0) {
				return true;
			}

			return strcasecmp($type, 'null') === 0 && $this->nullable;
		}

		if (strcasecmp($type, 'class') === 0) {
			return true;
		}

		return is_a($type, $this->type, true);
	}

	public function isBuiltin(): bool
	{
		return $this->builtin;
	}

	public function getType(): string
	{
		return $this->type;
	}

	public function toString(): string
	{
		return ($this->nullable ? '?' : '') . $this->type;
	}

	public function isNullable(): bool
	{
		return $this->nullable;
	}

}
