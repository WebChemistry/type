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

$builderIs = new AssertBuilder(
	function (string $given, string $accept): void {
		Assert::true(SimpleDataTypeFactory::fromString($given)->is($accept), sprintf('Trying pass %s to %s.', $accept, $given));
	},
	function (string $given, string $accept): void {
		Assert::false(SimpleDataTypeFactory::fromString($given)->is($accept), sprintf('Trying pass %s to %s.', $accept, $given));
	},
);

$builderAllows = new AssertBuilder(
	function (string $given, string $accept): void {
		Assert::true(SimpleDataTypeFactory::fromString($given)->allows($accept), sprintf('Trying pass %s to %s.', $accept, $given));
	},
	function (string $given, string $accept): void {
		Assert::false(SimpleDataTypeFactory::fromString($given)->allows($accept), sprintf('Trying pass %s to %s.', $accept, $given));
	},
);

foreach (array_diff(DataTypeHelper::BUILTIN_VALUES, ['null']) as $type) {
	$builderIs->add($type . '|null', ...arguments($type, 'null'));
	$builderAllows->add($type . '|null', ...arguments($type, 'null'));
	$builderIs->add('null|' . $type, ...arguments($type, 'null'));
	$builderAllows->add('null|' . $type, ...arguments($type, 'null'));
	$builderIs->add($type, ...arguments($type));
	$builderAllows->add($type, ...arguments($type));
}

enum CustomEnum: string
{
	case FOO = 'foo';
};

class A {} class B extends A {} class C extends B {}

$builderIs->add(C::class, [A::class, B::class, C::class], []);
$builderAllows->add(C::class, [C::class], [A::class, B::class]);

$builderIs->add(stdClass::class, ...arguments('class', stdClass::class));

$builderIs->add(CustomEnum::class, [BackedEnum::class, CustomEnum::class], []);
$builderAllows->add(CustomEnum::class, [CustomEnum::class], [BackedEnum::class]);

$builderIs->assert();
$builderAllows->assert();

Assert::null(SimpleDataTypeFactory::fromString('string|int'));
Assert::null(SimpleDataTypeFactory::fromString('string&int'));
Assert::null(SimpleDataTypeFactory::fromString('string|int|null'));
Assert::null(SimpleDataTypeFactory::fromString('string|'));
