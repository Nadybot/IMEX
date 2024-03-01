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
		if (!is_array($in) || Utils::arrayIsList($in)) {
			return JSON::export($in);
		}
		try {
			$builder = new TomlBuilder(4);
			foreach ($in as $key => $data) {
				if (!isset($data)) {
					$builder->addComment("{$key} = null");
				} elseif (!is_array($data)) {
					$builder->addValue($key, $data);
				} elseif (Utils::arrayIsList($data) && count(array_filter($data, "is_scalar"))) {
					$builder->addValue($key, $data);
				}
			}
			foreach ($in as $key => $data) {
				if (!isset($data) || !is_array($data)) {
					continue;
				}
				if (Utils::arrayIsList($data) && count(array_filter($data, "is_scalar"))) {
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
