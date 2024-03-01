<?php declare(strict_types=1);

namespace Nadylib\IMEX;

use Brick\VarExporter\VarExporter;

class PHP implements Imex {
	public static function import(string $in): mixed {
		$file = \tmpfile();
		if ($file === false) {
			throw new ImportException("Unable to create temporary file for parsing PHP code");
		}
		$path = stream_get_meta_data($file)['uri'];
		error_clear_last();
		if (fwrite($file, $in) === false) {
			$error = error_get_last();
			throw new ImportException(
				$error['message'] ?? 'Error writing to ' . $path,
				$error['type'] ?? 1
			);
		}
		error_clear_last();
		if (fclose($file) === false) {
			$error = error_get_last();
			throw new ImportException(
				$error['message'] ?? 'Error closing ' . $path,
				$error['type'] ?? 1
			);
		}
		try {
			include $path;
		} catch (\ParseError $e) {
			throw new ImportException($e->getMessage(), $e->getCode(), $e);
		}

		/** @var mixed $vars */
		return $vars;
	}

	public static function export(mixed $in): string {
		if (!is_array($in) || Utils::arrayIsList($in)) {
			return VarExporter::export($in);
		}
		$blocks = [];
		foreach ($in as $key => $value) {
			$blocks []= "\$vars[" . VarExporter::export($key) . "] = ".
				VarExporter::export($value) . ";\n";
		}
		return join("", $blocks);
	}
}
