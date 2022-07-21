<?php declare(strict_types = 1);

use Tester\Assert;
use WebChemistry\Type\DefaultDataTypeFactory;
use WebChemistry\Type\Single\SingleDataTypeAbstract;

require __DIR__ . '/../bootstrap.php';

$factory = new DefaultDataTypeFactory();

function union(string ... $union): string
{
	return implode('|', $union);
}

test('', function () use ($factory): void {

	$builtin = [
		'array',
		'callable',
		'bool',
		'true',
		'false',
		'float',
		'int',
		'string',
		'iterable',
		'object',
		'mixed',
		'null',
		'NULL',
	];

	foreach ($builtin as $type) {
		$object = $factory->createFromString($type);

		Assert::type(SingleDataTypeAbstract::class, $object);
		Assert::true($object->isSingle());
		Assert::true($object->isBuiltin());
		Assert::same(strtolower($type), $object->toString());
	}

});

test('complex', function () use ($factory): void {

	$object = $factory->createFromString('string|null');

	Assert::same(['string', 'null'], $object->getNames());
	Assert::false($object->isBuiltin());
	Assert::false($object->isSingle());
});

test('allowsAndEqualTo', function () use ($factory): void {

	$test = [
		'string' => [
			[],
			['bool', 'true', stdClass::class, 'mixed'],
		],
		'string|null' => [
			['string', 'null'],
			['string|bool', 'int', 'int|float', stdClass::class, 'mixed'],
		],
		stdClass::class => [
			[],
			[union(stdClass::class, 'null'), 'bool', 'mixed'],
		],
		'object' => [
			[stdClass::class],
			['bool', 'mixed', 'null'],
		],
		union('object', 'null') => [
			[stdClass::class, 'null'],
			['bool', 'mixed'],
		],
		union(stdClass::class, 'null') => [
			[stdClass::class, 'null'],
			['bool', union(stdClass::class, 'null', 'bool'), 'mixed'],
		],
		'iterable' => [
			['array'],
			[],
		],
		'mixed' => [
			[stdClass::class, 'null', 'bool', 'callable', 'true', 'string|null', union(stdClass::class, 'true')],
			[],
		],
	];

	foreach ($test as $expected => [$passedTrue, $passedFalse]) {
		Assert::true($factory->createFromString($expected)->allows($factory->createFromString($expected)));
		Assert::true($factory->createFromString($expected)->equalTo($factory->createFromString($expected)));

		foreach ($passedTrue as $item) {
			Assert::true(
				$factory->createFromString($expected)->allows($factory->createFromString($item)),
				sprintf('Trying pass %s to %s.', $item, $expected)
			);
			Assert::false(
				$factory->createFromString($expected)->equalTo($factory->createFromString($item)),
				sprintf('Trying pass %s to %s.', $item, $expected)
			);
		}

		foreach ($passedFalse as $item) {
			Assert::false(
				$factory->createFromString($expected)->allows($factory->createFromString($item)),
				sprintf('Trying pass %s to %s.', $item, $expected)
			);
			Assert::false(
				$factory->createFromString($expected)->equalTo($factory->createFromString($item)),
				sprintf('Trying pass %s to %s.', $item, $expected)
			);
		}
	}
});

var_dump($factory->createFromString('array<string>'));
