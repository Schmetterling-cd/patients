<?php

//function utf8_decode(string $string): string {}
//replacement: "mb_convert_encoding(%parameter0%, 'ISO-8859-1')", since: "8.2")

define('UTF8_ENCODED_CHARLIST','ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËéèêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ');

if (! function_exists ('mb_init'))
{
   function mb_init($locale = 'es_ES')
   {
      /*
       * Setting the Content-Type header with charset
       */
      setlocale(LC_CTYPE, $locale.'.UTF-8');
      iconv_set_encoding("output_encoding", "UTF-8");
      mb_internal_encoding('UTF-8');
      mb_regex_encoding('UTF-8');
      //header('Content-Type: text/html; charset=utf-8');
   }
}

if (! function_exists ('mb_ucfirst'))
{
   function mb_ucfirst ($str)
   {
      return mb_convert_encoding (ucfirst (mb_convert_encoding($str, 'ISO-8859-1')), 'UTF-8', 'ISO-8859-1');
   }
}

if (! function_exists ('mb_lcfirst'))
{
   function mb_lcfirst ($str)
   {
      return mb_convert_encoding (lcfirst (mb_convert_encoding($str, 'ISO-8859-1')), 'UTF-8', 'ISO-8859-1');
   }
}

if (! function_exists ('mb_ucwords'))
{
   function mb_ucwords ($str)
   {
      return mb_convert_case($str, MB_CASE_TITLE, "UTF-8");
   }
}

if (! function_exists ('mb_strip_accents'))
{
   function mb_strip_accents ($string)
   {
      return mb_strtr ($string, UTF8_ENCODED_CHARLIST, 'AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn');
   }
}

if (! function_exists ('mb_strtr'))
{
   function mb_strtr ($str, $from, $to = null)
   {
      if(is_array($from))
      {
         foreach($from as $k => $v)
         {
            $utf8_from[mb_convert_encoding($k, 'ISO-8859-1')]=mb_convert_encoding($v, 'ISO-8859-1');
         }
         return mb_convert_encoding (strtr (mb_convert_encoding ($str, 'ISO-8859-1'), $utf8_from), 'UTF-8', 'ISO-8859-1');
      }
      return mb_convert_encoding (strtr (mb_convert_encoding ($str, 'ISO-8859-1'), mb_convert_encoding($from, 'ISO-8859-1'), mb_convert_encoding ($to, 'ISO-8859-1')), 'UTF-8', 'ISO-8859-1');
   }
}

if (!function_exists('mb_preg_replace')) {
	function mb_preg_replace($pattern, $replacement, $subject, $limit = -1, &$count = null) {
		if (is_array($pattern)) {
			foreach ($pattern as $k => $v) {
				$utf8_pattern[mb_convert_encoding($k, 'ISO-8859-1')] = mb_convert_encoding($v, 'ISO-8859-1');
			}
		} else {
			$utf8_pattern = mb_convert_encoding($pattern, 'ISO-8859-1');
		}

		if (is_array($replacement)) {
			foreach ($replacement as $k => $v) {
				$utf8_replacement[mb_convert_encoding($k, 'ISO-8859-1')] = mb_convert_encoding($v, 'ISO-8859-1');
			}
		} else {
			$utf8_replacement = mb_convert_encoding($replacement, 'ISO-8859-1');
		}

		if (is_array($subject)) {
			foreach ($subject as $k => $v) {
				$utf8_subject[mb_convert_encoding($k, 'ISO-8859-1')] = mb_convert_encoding($v, 'ISO-8859-1');
			}
		} else {
			$utf8_subject = mb_convert_encoding($subject, 'ISO-8859-1');
		}
		/**
		 * @var string|array|null $r
		 */
		$r = preg_replace($utf8_pattern,$utf8_replacement,$utf8_subject,$limit,$count);

		if (is_array($r)) {
			foreach ($r as $k => $v) {
				$return[mb_convert_encoding($k, 'UTF-8')] = mb_convert_encoding($v, 'UTF-8');
			}
		} else {
			$return = mb_convert_encoding($r, 'UTF-8');
		}

		return $return;
	}
}

if (! function_exists ('mb_html_entity_decode'))
{
   function mb_html_entity_decode ($string, $quote_style = ENT_COMPAT, $charset = 'UTF-8')
   {
      return html_entity_decode ($string, $quote_style, $charset);
   }
}

if (! function_exists ('mb_htmlentities'))
{
   function mb_htmlentities ($string, $quote_style = ENT_COMPAT, $charset = 'UTF-8', $double_encode = true)
   {
      return htmlentities ($string, $quote_style, $charset, $double_encode);
   }
}

if (! function_exists ('mb_trim'))
{
   function mb_trim ($string, $charlist = null)
   {
      if($charlist == null)
      {
         return mb_convert_encoding(trim (mb_convert_encoding($string, 'ISO-8859-1')), 'UTF-8', 'ISO-8859-1');
      }
      return mb_convert_encoding(trim (mb_convert_encoding($string, 'ISO-8859-1'), mb_convert_encoding($string, 'ISO-8859-1')), 'UTF-8', 'ISO-8859-1');
   }
}

/************************ EXPERIMENTAL ZONE ************************/

if (! function_exists('mb_strip_tags_all'))
{
   function mb_strip_tags_all($document,$repl = ''){
      $search = array('@<script[^>]*?>.*?</script>@si',  // Strip out javascript
                     '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
                     '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
                     '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments including CDATA
      );
      $text = mb_preg_replace($search, $repl, $document);
      return $text;
   }
}

if (! function_exists('mb_strip_tags'))
{
   function mb_strip_tags($document,$repl = ''){
      $search = array('@<script[^>]*?>.*?</script>@si',  // Strip out javascript
                     '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
                     '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
                     '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments including CDATA
      );
      $text = mb_preg_replace($search, $repl, $document);
      return $text;
   }
}

if (! function_exists('mb_strip_urls'))
{
   function mb_strip_urls($txt, $repl = ' ')
   {
      $txt = mb_preg_replace('@http[s]?://[^\s<>"\']*@',$repl,$txt);
      return $txt;
   }
}

// parse strings as identifiers
if(!function_exists('mb_string_url'))
{
   function mb_string_url($string, $to_lower = true)
   {
      $string = mb_strtolower($string);
      $string = mb_strip_accents($string);
      $string = preg_replace('@[^a-z0-9]@',' ',$string);
      $string = preg_replace('@\s+@','-',$string);
      return $string;
   }
}

if(!function_exists('mb_preg_match_all'))
{
	function mb_preg_match_all($ps_pattern, $ps_subject, &$pa_matches, $pn_flags = PREG_PATTERN_ORDER, $pn_offset = 0, $ps_encoding = 'utf-8') {
		// WARNING! - All this function does is to correct offsets, nothing else:
		//
		if (is_null($ps_encoding))
			$ps_encoding = mb_internal_encoding();

		$pn_offset = strlen(mb_substr($ps_subject, 0, $pn_offset, $ps_encoding));
		$ret = preg_match_all($ps_pattern, $ps_subject, $pa_matches, $pn_flags, $pn_offset);

		if ($ret && ($pn_flags & PREG_OFFSET_CAPTURE))
			foreach($pa_matches as &$ha_match)
				foreach($ha_match as &$ha_match)
					$ha_match[1] = mb_strlen(substr($ps_subject, 0, $ha_match[1]), $ps_encoding);
		//
		// (code is independent of PREG_PATTER_ORDER / PREG_SET_ORDER)

		return $ret;
	}
}

if(!function_exists('str_split_unicode'))
{
	function str_split_unicode($str, $l = 0) {
		if ($l > 0) {
			$ret = array();
			$len = mb_strlen($str, "UTF-8");
			for ($i = 0; $i < $len; $i += $l) {
				$ret[] = mb_substr($str, $i, $l, "UTF-8");
			}
			return $ret;
		}
		return preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
	}
}

if (!function_exists('array_key_first')) {
	/**
	 * @param array $array
	 * @return (int|string)|null
	 */
	function array_key_first($array)
	{
		if (!is_array($array) || !count($array)) {
			return null;
		}
		/**
		 * @var mixed $keys
		 */
		$keys = array_keys($array);
		return $keys[0];
	}
}

if (! function_exists("array_key_last")) {
	/**
	* @param array $array
	* @return (int|string)|null
	*/
	function array_key_last($array) {
		if (!is_array($array) || empty($array)) {
			return null;
		}

		/**
		 * @var mixed
		 */
		return array_keys($array)[count($array)-1];
	}
}