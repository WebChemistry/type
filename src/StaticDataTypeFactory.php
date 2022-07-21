<?php declare(strict_types = 1);

namespace WebChemistry\Type;

use DomainException;

final class StaticDataTypeFactory
{

	public static function fromString(string $type): DataType
	{
		if (str_contains($type, '&')) {
			throw new DomainException('Intersections are not currently supported.');
		}

		if (str_starts_with($type, '?')) {
			$type = substr($type, 1) . '|null';
		}

		$types = array_map(
			fn (string $type) => new SingleDataType($type),
			explode('|', $type),
		);

		return count($types) > 1 ? new UnionDataType($types) : $types[0];
	}

}
