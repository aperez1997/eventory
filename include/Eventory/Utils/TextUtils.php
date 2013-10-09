<?php
/**
 * @author Tony Perez <aperez1997@yahoo.com>

 */

namespace Eventory\Utils;

class TextUtils
{
	public static function RemoveWordsFromText($text, array $toRemove)
	{
		$words = explode(' ', $text);
		$hash = array_combine($toRemove, $toRemove);
		foreach ($words as $k => $word){
			if (isset($hash[strtolower($word)])){
				unset ($words[$k]);
			}
		}
		return join(' ', $words);
	}

	public static function FindNamesInText($text)
	{
		$text = self::RemoveWordsFromText($text, self::$INVALID_FIRST_NAMES);

		$regex = '/([A-Z][a-z]+ [A-Z][a-z]+)/';
		$rv = preg_match_all($regex, $text, $matches);
		if ($rv > 0){
			return $matches[0];
		} else {
			return array();
		}
	}

	protected static $INVALID_FIRST_NAMES = array(
		'but', 'however',
		'the', 'this', 'then', 'these',
		'stay',
		'who', 'what', 'where', 'when', 'why',
		'once', 'initially', 'eventually', 'soon', 'until',
		'at', 'ask', 'after', 'by', 'to', 'my',
		'giving', 'luckily', 
	);
}
