<?php

if (! function_exists ('env')) {
	function env($key, $default = null) {
		if (array_key_exists($key, $_ENV)) {
			switch (strtolower($_ENV[$key])) {
				case 'true':
				case '(true)':
					return true;
				case 'false':
				case '(false)':
					return false;
				case 'empty':
				case '(empty)':
					return '';
				case 'null':
				case '(null)':
					return;
			}
			return $_ENV[$key];
		}
		return $default;
	}
}

if (! function_exists ('mb_str_split')) {
	function mb_str_split($str, $length = 1, $ps_encoding = 'utf-8') {
		$result = preg_match_all("/.{{$length}}/su", $str, $matches);
		if ($result) {
			$result = $matches[0];
			if ($length > 1) {
				$stub = mb_strlen($str, $ps_encoding) % $length;
				if ($stub) {
					$result[] = mb_substr($str, -$stub, $stub, $ps_encoding);
				}
			}
		} else {
			$result = array($str);
		}
		return $result;
	}
}

if (!function_exists ('mb_preg_match')) {
	function mb_preg_match($ps_pattern, $ps_subject, array &$pa_matches = null, $pn_flags = 0, $pn_offset = 0, $ps_encoding = 'utf-8') {
		if (is_null($ps_encoding)) {
			$ps_encoding = mb_internal_encoding();
		}

		$pn_offset = strlen(mb_substr($ps_subject, 0, $pn_offset, $ps_encoding));
		$ret = preg_match($ps_pattern . 'u', $ps_subject, $pa_matches, $pn_flags, $pn_offset);

		if ($ret && ($pn_flags & PREG_OFFSET_CAPTURE))
			foreach ($pa_matches as &$ha_subpattern)
				$ha_subpattern[1] = mb_strlen(substr($ps_subject, 0, $ha_subpattern[1]), $ps_encoding);
		return $ret;
	}
}

if (! function_exists ('mb_ord')) {
	function mb_ord($char, $ps_encoding = 'utf-8') {

		$c = unpack("N", mb_convert_encoding($char, 'UCS-4BE', 'UTF-8'));
		return $c[1];

	}
}

if (! function_exists ('mb_str_replace')) {
	function mb_str_replace($needle, $replacement, $haystack, $ps_encoding = 'utf-8') {

		$needle_len = mb_strlen($needle);
		$replacement_len = mb_strlen($replacement);
		$pos = mb_strpos($haystack, $needle);
		while ($pos !== false) {
			$haystack = mb_substr($haystack, 0, $pos) . $replacement . mb_substr($haystack, $pos + $needle_len);
			$pos = mb_strpos($haystack, $needle, $pos + $replacement_len);
		}
		return $haystack;

	}
}

if (! function_exists ('mb_trim')) {
	function mb_trim($string, $chars = "", $chars_array = array(), $ps_encoding = 'utf-8') {
		for ($x = 0; $x < iconv_strlen($chars); $x++) $chars_array[] = preg_quote(iconv_substr($chars, $x, 1));
		$encoded_char_list = implode("|", array_merge(array("\s", "\t", "\n", "\r", "\0", "\x0B"), $chars_array));

		$string = mb_ereg_replace("^($encoded_char_list)*", "", $string);
		$string = mb_ereg_replace("($encoded_char_list)*$", "", $string);
		return $string;
	}
}

if (! function_exists ('mb_ucfirst')) {
	function mb_ucfirst($str, $e = 'utf-8') {
		$fc = mb_strtoupper(mb_substr($str, 0, 1, $e), $e);
		return $fc . mb_substr($str, 1, mb_strlen($str, $e), $e);
	}
}

//http://4rapiddev.com/php/php-php-validate-ip-address-by-using-regular-expression/
function validateIpAddress($ip_addr)
{
	//first of all the format of the ip address is matched
	if(preg_match("/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/",$ip_addr))
	{
		//now all the intger values are separated
		$parts=explode(".",$ip_addr);
		//now we need to check each part can range from 0-255
		foreach($parts as $ip_parts)
		{
			if(intval($ip_parts)>255 || intval($ip_parts)<0)
			return false; //if number is not within range of 0-255
		}
		return true;
	}
	else
		return false; //if format of ip address doesn't matches
}

/**
 * Функция пользовательской сортировки массива по дате. Используется как callback в usort();
 * @param $a array
 * @param $b array
 */
function arrayDateCompare($a, $b) {
	
    if ($a['date'] == $b['date']) {
        return 0;
    }
    return ($a > $b) ? -1 : 1;
    
}

/**
 * Функция пользовательской сортировки по возрастанию массива по дате вида yyyy.mm.dd. Используется как callback в usort();
 * @param $a array
 * @param $b array
 */
function arrayDateEuCompareAsc($a, $b) {
	
	if ($a['date'] == $b['date']) {
        return 0;
    }
    
    $date1 = explode('.', $a['date']);
    $date2 = explode('.', $b['date']);
    
    if ($date1[2] > $date2[2]) {
    	return 1;
    } else {
    	if ($date2[2] > $date1[2]) {
    		return -1;
    	} else {
    		if ($date1[1] > $date2[1]) {
    			return 1;
    		} else {
    			if ($date2[1] > $date1[1]) {
    				return -1;
    			} else {
    				if ($date1[0] > $date2[0]) {
    					return 1;
    				} else {
    					if ($date2[0] > $date1[0]) {
    						return -1;
    					} else {
    						return 0;
    					}
    				}
    			}
    		}
    		
    	}
    }
}

/**
 * Функция пользовательской сортировки по убыванию массива по дате вида yyyy.mm.dd. Используется как callback в usort();
 * @param $a array
 * @param $b array
 */
function arrayDateEuCompareDesc($a, $b) {
	
	if ($a['date'] == $b['date']) {
        return 0;
    }
    
    $date1 = explode('.', $a['date']);
    $date2 = explode('.', $b['date']);
    
    if ($date1[2] > $date2[2]) {
    	return -1;
    } else {
    	if ($date2[2] > $date1[2]) {
    		return 1;
    	} else {
    		if ($date1[1] > $date2[1]) {
    			return -1;
    		} else {
    			if ($date2[1] > $date1[1]) {
    				return 1;
    			} else {
    				if ($date1[0] > $date2[0]) {
    					return -1;
    				} else {
    					if ($date2[0] > $date1[0]) {
    						return 1;
    					} else {
    						return 0;
    					}
    				}
    			}
    		}
    		
    	}
    }
}

// "Группировка" элементов массива
// * на входе:
// array(5, 5, 7, 8, 13, 9, 9, 7)
// * на выходе:
// Array
// (
//     [0] => Array
//         (
//             [value] => 5
//             [number] => 0
//             [2group] => 1
//             [periodLength] => 2
//         )
//
//     [2] => Array
//         (
//             [value] => 7
//             [number] => 2
//             [2group] =>
//         )
//
//     [3] => Array
//         (
//             [value] => 8
//             [number] => 3
//             [2group] =>
//         )
//
//     [4] => Array
//         (
//             [value] => 13
//             [number] => 4
//             [2group] =>
//         )
//
//     [5] => Array
//         (
//             [value] => 9
//             [number] => 5
//             [2group] => 1
//             [periodLength] => 2
//         )
//
//     [7] => Array
//         (
//             [value] => 7
//             [number] => 7
//             [2group] =>
//         )
//
// )
function groupArrayElements($data) {

	$dataProc = array(); //конечный массив
	

	$i = 0;
	foreach ($data as $item) {
		$dataProc[] = array('value' => $item, //оригинальное значение из исходного массива
'number' => (int) $i++)//порядковый номер
;
	}
	
	//помечаем элементы которые нужно сгруппировать
	for ($i = 0; $i < count($dataProc); $i++) {
		if ((!isset($dataProc[$i + 1]['value']) || $dataProc[$i]['value'] != $dataProc[$i + 1]['value']) && (!isset($dataProc[$i - 1]['value']) || $dataProc[$i]['value'] !== $dataProc[$i - 1]['value'])) {
			$dataProc[$i]['2group'] = false;
		} else {
			$dataProc[$i]['2group'] = true;
		}
	}
	
	for ($i = 0; $i < count($dataProc); $i++) {
		if ((!isset($dataProc[$i - 1]['value']) || $dataProc[$i]['value'] !== $dataProc[$i - 1]['value']) && $dataProc[$i]['2group'] == true) {
			$periodLength = 0;
			for ($j = $i; $j < count($dataProc); $j++) {
				if ($dataProc[$i]['value'] != $dataProc[$j]['value'] || $dataProc[$j]['2group'] == false) {
					break;
				} else {
					$periodLength++;
				}
			}
			$dataProc[$i]['periodLength'] = $periodLength;
		}
	}
	
	//убираем то что не нужно
	foreach ($dataProc as $key => $item) {
		if ($item['2group'] == true && !isset($item['periodLength'])) {
			unset($dataProc[$key]);
		}
	}
	
	return $dataProc;
}

	//http://php.net/manual/en/book.simplexml.php
// 2014.12.17 - добавлен параметр
// $keyNameToLowerCase - если установлен, имя ключа переводит в нижний регистр.
function simpleXMLToArray(SimpleXMLElement $xml, $attributesKey = null, $childrenKey = null, $valueKey = null, $keyNameToLowerCase = false) {

	if ($childrenKey && !is_string($childrenKey)) {
		$childrenKey = '@children';
	}
	if ($attributesKey && !is_string($attributesKey)) {
		$attributesKey = '@attributes';
	}
	if ($valueKey && !is_string($valueKey)) {
		$valueKey = '@values';
	}

	/**
	 * @psalm-var array $return
	 */
	$return = array();
	$name = $xml->getName();
	$_value = trim((string) $xml);
	if (!strlen($_value)) {
		$_value = null;
	}
	
	if ($_value !== null) {
		if ($valueKey) {
			$return[$valueKey] = $_value;
		} else {
			/**
			 * @psalm-var array $return
			 */
			$return = $_value;
		}
	}
	
	$children = array();
	$first = true;
	foreach ($xml->children() as $elementName => $child) {
		$value = simpleXMLToArray($child, $attributesKey, $childrenKey, $valueKey, $keyNameToLowerCase);
		if ($keyNameToLowerCase) $elementName = strtolower($elementName);
		if (isset($children[$elementName])) {
			if (is_array($children[$elementName])) {
				if ($first) {
					$temp = $children[$elementName];
					unset($children[$elementName]);
					$children[$elementName][] = $temp;
					$first = false;
				}
				$children[$elementName][] = $value;
			} else {
				$children[$elementName] = array($children[$elementName], $value);
			}
		} else {
			$children[$elementName] = $value;
		}
	}
	if ($children) {
		if ($childrenKey) {
			$return[$childrenKey] = $children;
		} else {
			$return = array_merge($return, $children);
		}
	}
	
	$attributes = array();
	foreach ($xml->attributes() as $name => $value) {
		if ($keyNameToLowerCase) $name = strtolower($name);
		$attributes[$name] = trim($value);
	}
	if ($attributes) {
		if (!is_array($return)) $return = array('@values' => $return);

		if ($attributesKey) {
			$return[$attributesKey] = $attributes;
		} else {
			$return = array_merge($return, $attributes);
		}
	}
	
	return $return;
}

//http://www.php.net/manual/en/function.lcfirst.php
if ( false === function_exists('lcfirst') ):
    function lcfirst( $str )
    { return (string)(strtolower(substr($str,0,1)).substr($str,1));}
endif;