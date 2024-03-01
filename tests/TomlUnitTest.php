<?php declare(strict_types=1);

namespace Nadylib\IMEX\Tests;

use Nadylib\IMEX\TOML;
use PHPUnit\Framework\Attributes\{DataProvider, Small};
use PHPUnit\Framework\TestCase;

/** @Small */
final class TomlUnitTest extends TestCase {
	/** @return array<string,array{0:string,1:array<string,mixed>}> */
	public static function getTomlExamples(): array {
		return [
			"Case 1" => [
				"/\s*a\s*=\s*1\n\s*\[b\]\n\s*c\s*=\s*1\s*/s",
				["a" => 1, "b" => ["c" => 1]],
			],
			"Don't garble simple keys" => [
				"/^\s*a\s*=\s*1\n\s*\[b\]\n\s*c\s*=\s*1\s*$/s",
				["b" => ["c" => 1], "a" => 1],
			],
			"Data types" => [
				'/^\s*a\s*=\s*1\n\s*b\s*=\s*true\n\s*c\s*=\s*"yes"\n\s*d\s*=\s*\[\s*false\s*,\s*true\s*,\s*false\s*\]\s*$/s',
				["a" => 1, "b" => true, "c" => "yes", "d" => [false, true, false]],
			],
			"Arrays" => [
				'/^\s*\[\[a\]\]\n\s*a\s*=\s*true\n\s*\[\[a\]\]\n\s*a\s*=\s*false\n\s*\[\[a\]\]\n\s*a\s*=\s*true\s*$/s',
				["a" => [["a" => true], ["a" => false], ["a" => true]]],
			],
		];
	}

	/** @dataProvider getTomlExamples */
	public function testSerializing(string $resultMask, mixed $data): void {
		$toml = TOML::export($data);
		$this->assertMatchesRegularExpression($resultMask, $toml);
	}
}
