<?php

namespace app\core\helpers;

final class ServiceFormatHelper
{

	public static function getCamelCase(string $string, string $separator): string
	{

		$parts = explode($separator, $string);

		foreach ($parts as $key => &$part) {
			if ($key == 0) {
				continue;
			}

			$part = ucfirst($part);
		}

		return implode($parts);

	}

	public static function formatDate(string $date, string $format)
	{

		return date_create($date)->format($format);

	}

}