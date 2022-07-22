<?php declare(strict_types = 1);

use Tester\Assert;
use WebChemistry\Type\Helper\DataTypeHelper;
use WebChemistry\Type\Simple\SimpleDataTypeFactory;

require __DIR__ . '/../bootstrap.php';

function arguments(string ... $types): array
{
	$builtin = array_merge(DataTypeHelper::BUILTIN_VALUES, ['class', PhpToken::class]);

	return [$types, array_diff($builtin, $types)];
}

$builder = new AssertBuilder(
	function (string $given, string $accept): void {
		Assert::true(SimpleDataTypeFactory::fromString($given)->is($accept), sprintf('Trying pass %s to %s.', $accept, $given));
	},
	function (string $given, string $accept): void {
		Assert::false(SimpleDataTypeFactory::fromString($given)->is($accept), sprintf('Trying pass %s to %s.', $accept, $given));
	},
);

foreach (array_diff(DataTypeHelper::BUILTIN_VALUES, ['null']) as $type) {
	$builder->add($type . '|null', ...arguments($type, 'null'));
	$builder->add('null|' . $type, ...arguments($type, 'null'));
	$builder->add($type, ...arguments($type));
}

$builder->add(stdClass::class, ...arguments('class', stdClass::class));
$builder->add(DateTimeInterface::class, ...arguments('class', DateTimeInterface::class, DateTime::class, DateTimeImmutable::class));
$builder->add(DateTime::class, [DateTime::class], [DateTimeInterface::class]);

$builder->assert();

Assert::null(SimpleDataTypeFactory::fromString('string|int'));
Assert::null(SimpleDataTypeFactory::fromString('string&int'));
Assert::null(SimpleDataTypeFactory::fromString('string|int|null'));
Assert::null(SimpleDataTypeFactory::fromString('string|'));
