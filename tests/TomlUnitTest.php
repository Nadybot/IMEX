<?php declare(strict_types=1);

namespace Nadylib\IMEX\Tests;

use Nadylib\IMEX\TOML;
use PHPUnit\Framework\TestCase;

/**
 * @Small
 */
final class TomlUnitTest extends TestCase {
	/** @return array<string,array{0:string,1:array<string,mixed>}> */
	public static function getTomlExamples(): array {
		return [
			'Case 1' => [
				"/\s*a\s*=\s*1\n\s*\[b\]\n\s*c\s*=\s*1\s*/s",
				['a' => 1, 'b' => ['c' => 1]],
			],
			"Don't garble simple keys" => [
				"/^\s*a\s*=\s*1\n\s*\[b\]\n\s*c\s*=\s*1\s*$/s",
				['b' => ['c' => 1], 'a' => 1],
			],
			'Data types' => [
				'/^\s*a\s*=\s*1\n\s*b\s*=\s*true\n\s*c\s*=\s*"yes"\n\s*d\s*=\s*\[\s*false\s*,\s*true\s*,\s*false\s*\]\s*$/s',
				['a' => 1, 'b' => true, 'c' => 'yes', 'd' => [false, true, false]],
			],
			'Arrays' => [
				'/^\s*\[\[a\]\]\n\s*a\s*=\s*true\n\s*\[\[a\]\]\n\s*a\s*=\s*false\n\s*\[\[a\]\]\n\s*a\s*=\s*true\s*$/s',
				['a' => [['a' => true], ['a' => false], ['a' => true]]],
			],
			'Enums' => [
				'/^\s*a\s*=\s*"one"\s*\n\s*b\s*=\s*2\s*$/s',
				['a' => Helper\StringEnum::ONE, 'b' => Helper\IntEnum::TWO],
			],
			'Complex' => [
				'/./',
				[
					'org_id' => 1_390_595,
					'database' => [
						'type' => 'sqlite',
						'name' => 'nadybot.db',
						'host' => './data/',
						'username' => null,
						'password' => null,
					],
					'paths' => [
						'cache' => './cache/',
						'html' => './html/',
						'data' => './data/',
						'logs' => './logs/',
						'modules' => [
							'./src/Modules',
							'./extras',
						],
					],
					'main' => [
						'login' => 'xxx',
						'password' => 'yyy',
						'character' => 'Zzzz',
						'dimension' => 5,
						'web_login' => null,
						'web_password' => null,
					],
					'general' => [
						'org_name' => 'Nadybot Testers',
						'super_admins' => 'Dummy',
						'show_aoml_markup' => false,
						'default_module_status' => 1,
						'enable_console_client' => true,
						'enable_package_module' => true,
						'enable_hydrator_cache' => true,
						'auto_org_name' => false,
						'timezone' => 'UTC',
					],
					'proxy' => [
						'enabled' => false,
						'server' => '127.0.0.1',
						'port' => 9_993,
					],
					'auto-unfreeze' => [
						'enabled' => true,
						'use_nadyproxy' => false,
					],
					'worker' => [],
					'settings' => [
						'webserver_auth' => 'aoauth',
						'console_color' => true,
						'console_bg_color' => true,
					],
				],
			],
		];
	}

	/** @dataProvider getTomlExamples */
	public function testSerializing(string $resultMask, mixed $data): void {
		$toml = TOML::export($data);
		$this->assertMatchesRegularExpression($resultMask, $toml);
	}
}
