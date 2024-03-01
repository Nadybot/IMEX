<?php declare(strict_types=1);

namespace Nadylib\IMEX\Tests;

use Nadylib\IMEX\PHP;
use PHPUnit\Framework\TestCase;

/** @Small */
final class PhpUnitTest extends TestCase {
	/** @return array<string,array{0:string,1:mixed}> */
	public static function getPhpExamples(): array {
		return [
			"scalar int" => ["1", 1],
			"scalar string" => ["'1'", "1"],
			"scalar bool" => ["true", true],
			"scalar float" => ["1.23", 1.23],
			"lists" => ["[\n    true,\n    1,\n    '2'\n]", [true, 1, "2"]],
			"simple hash" => [
				"\$vars['a'] = 1;\n\$vars['b'] = 2;\n\$vars['c'] = 3;",
				["a" => 1, "b" => 2, "c" => 3],
			],
			"complex hash" => [
				"\$vars['a'] = [\n    'b' => 1,\n    'c' => 2\n];",
				["a" => ["b" => 1, "c" => 2]],
			],
		];
	}

	/** @dataProvider getPhpExamples */
	public function testSerializing(string $result, mixed $data): void {
		$php = trim(PHP::export($data));
		$this->assertSame($result, $php);
	}

	public function testImporting(): void {
		$code = "<?" . "php\n\n".
			'$vars = [];' . PHP_EOL.
			'$vars["a"] = 24;' . PHP_EOL;
		$vars = PHP::import($code);
		$this->assertSame(["a" => 24], $vars);
	}
}
