<?php declare(strict_types=1);

namespace Nadylib\IMEX;

use Yosymfony\Toml\{Toml as YoToml, TomlBuilder};

class TOML implements Imex {
	public static function import(string $in): mixed {
		try {
			return YoToml::parse($in, false);
		} catch (\Throwable $e) {
			throw new ImportException($e->getMessage(), $e->getCode(), $e);
		}
	}

	public static function export(mixed $in): string {
		if (!is_array($in) || self::arrayIsList($in)) {
			return JSON::export($in);
		}
		try {
			$builder = new TomlBuilder(4);
			foreach ($in as $key => $data) {
				if (!is_array($data)) {
					$builder->addValue($key, $data);
				} elseif (self::arrayIsList($data) && count(array_filter($data, "is_scalar"))) {
					$builder->addValue($key, $data);
				}
			}
			foreach ($in as $key => $data) {
				if (!is_array($data)) {
					continue;
				}
				if (self::arrayIsList($data) && count(array_filter($data, "is_scalar"))) {
					continue;
				}
				if (array_is_list($data)) {
					foreach ($data as $block) {
						$builder->addArrayOfTable($key);
						foreach ($block as $key => $value) {
							$builder->addValue($key, $value);
						}
					}
				} else {
					$builder->addTable($key);
					foreach ($data as $key => $value) {
						$builder->addValue($key, $value);
					}
				}
			}
			return $builder->getTomlString();
		} catch (\Throwable $e) {
			throw new ExportException($e->getMessage(), $e->getCode(), $e);
		}
	}

	/** @param array<mixed> $array */
	private static function arrayIsList(array $array): bool {
		if (function_exists('array_ist_list')) {
			return \array_is_list($array);
		}
		$i = -1;
		foreach ($array as $k => $v) {
			if ($k !== ++$i) {
				return false;
			}
		}
		return true;
	}
}
