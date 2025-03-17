<?php declare(strict_types=1);

namespace Nadylib\IMEX;

use InvalidArgumentException;
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
		if (!is_array($in) || Utils::arrayIsList($in)) {
			return JSON::export($in);
		}
		try {
			$builder = new TomlBuilder(4);
			foreach ($in as $key => $data) {
				if (!isset($data)) {
					$builder->addComment("{$key} = null");
				} elseif (!is_array($data)) {
					if (!is_bool($data) && !is_float($data) && !is_int($data) && !is_string($data)) {
						throw new InvalidArgumentException('Cannot convert ' . gettype($data) . ' to TOML');
					}
					$builder->addValue($key, $data);
				} elseif (Utils::arrayIsList($data) && count(array_filter($data, 'is_scalar'))) {
					$builder->addValue($key, $data);
				}
			}
			foreach ($in as $key => $data) {
				if (!isset($data) || !is_array($data)) {
					continue;
				}
				if (Utils::arrayIsList($data) && count(array_filter($data, 'is_scalar'))) {
					continue;
				}
				if (array_is_list($data)) {
					foreach ($data as $block) {
						$builder->addArrayOfTable($key);
						foreach ($block as $subkey => $value) {
							if (isset($value)) {
								$builder->addValue($subkey, $value);
							} else {
								$builder->addComment("{$subkey} = null");
							}
						}
					}
				} else {
					$builder->addTable($key);
					foreach ($data as $subkey => $value) {
						if (isset($value)) {
							if (!is_array($value) && !is_bool($value) && !is_float($value) && !is_int($value) && !is_string($value)) {
								throw new InvalidArgumentException('Cannot convert ' . gettype($value) . ' to TOML');
							}
							$builder->addValue($subkey, $value);
						} else {
							$builder->addComment("{$subkey} = null");
						}
					}
				}
			}
			return $builder->getTomlString();
		} catch (\Throwable $e) {
			throw new ExportException($e->getMessage(), $e->getCode(), $e);
		}
	}
}
