<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Helper;

/**
 * Core data helper
 */
class String extends \Magento\Core\Helper\AbstractHelper
{
    /**
     * @var \Magento\Core\Model\LocaleInterface
     */
    protected $_locale;

    /**
     * Magento string lib
     *
     * @var \Magento\Stdlib\StringIconv
     */
    protected $stringIconv;

    /**
     * @param \Magento\Core\Helper\Context $context
     * @param \Magento\Core\Model\Locale $locale
     * @param \Magento\Stdlib\StringIconv $stringIconv
     */
    public function __construct(
        \Magento\Core\Helper\Context $context,
        \Magento\Core\Model\Locale $locale,
        \Magento\Stdlib\StringIconv $stringIconv
    ) {
        parent::__construct($context);
        $this->_locale = $locale;
        $this->stringIconv = $stringIconv;
    }

    /**
     * Split string and appending $insert string after $needle
     *
     * @param string $str
     * @param integer $length
     * @param string $needle
     * @param string $insert
     * @return string
     */
    public function splitInjection($str, $length = 50, $needle = '-', $insert = ' ')
    {
        $str = $this->strSplit($str, $length);
        $newStr = '';
        foreach ($str as $part) {
            if ($this->stringIconv->strlen($part) >= $length) {
                $lastDelimiter = $this->stringIconv->strpos($this->stringIconv->strrev($part), $needle);
                $tmpNewStr = $this->stringIconv->substr($this->stringIconv->strrev($part), 0, $lastDelimiter)
                    . $insert . $this->stringIconv->substr($this->stringIconv->strrev($part), $lastDelimiter);
                $newStr .= $this->stringIconv->strrev($tmpNewStr);
            } else {
                $newStr .= $part;
            }
        }
        return $newStr;
    }



    /**
     * Binary-safe variant of strSplit()
     * + option not to break words
     * + option to trim spaces (between each word)
     * + option to set character(s) (pcre pattern) to be considered as words separator
     *
     * @param string $str
     * @param int $length
     * @param bool $keepWords
     * @param bool $trim
     * @param string $wordSeparatorRegex
     * @return array
     */
    public function strSplit($str, $length = 1, $keepWords = false, $trim = false, $wordSeparatorRegex = '\s')
    {
        $result = array();
        $strLen = $this->stringIconv->strlen($str);
        if ((!$strLen) || (!is_int($length)) || ($length <= 0)) {
            return $result;
        }
        // trim
        if ($trim) {
            $str = trim(preg_replace('/\s{2,}/siu', ' ', $str));
        }
        // do a usual str_split, but safe for our encoding
        if ((!$keepWords) || ($length < 2)) {
            for ($offset = 0; $offset < $strLen; $offset += $length) {
                $result[] = $this->stringIconv->substr($str, $offset, $length);
            }
        }
        // split smartly, keeping words
        else {
            $split = preg_split('/(' . $wordSeparatorRegex . '+)/siu', $str, null, PREG_SPLIT_DELIM_CAPTURE);
            $i        = 0;
            $space    = '';
            $spaceLen = 0;
            foreach ($split as $key => $part) {
                if ($trim) {
                    // ignore spaces (even keys)
                    if ($key % 2) {
                        continue;
                    }
                    $space    = ' ';
                    $spaceLen = 1;
                }
                if (empty($result[$i])) {
                    $currentLength = 0;
                    $result[$i]    = '';
                    $space         = '';
                    $spaceLen      = 0;
                }
                else {
                    $currentLength = $this->stringIconv->strlen($result[$i]);
                }
                $partLength = $this->stringIconv->strlen($part);
                // add part to current last element
                if (($currentLength + $spaceLen + $partLength) <= $length) {
                    $result[$i] .= $space . $part;
                }
                // add part to new element
                elseif ($partLength <= $length) {
                    $i++;
                    $result[$i] = $part;
                }
                // break too long part recursively
                else {
                    foreach ($this->strSplit($part, $length, false, $trim, $wordSeparatorRegex) as $subpart) {
                        $i++;
                        $result[$i] = $subpart;
                    }
                }
            }
        }
        // remove last element, if empty
        $count = count($result);
        if ($count) {
            if ($result[$count - 1] === '') {
                unset($result[$count - 1]);
            }
        }
        // remove first element, if empty
        if (isset($result[0]) && $result[0] === '') {
            array_shift($result);
        }
        return $result;
    }

    /**
     * Sorts array with multibyte string keys
     *
     * @param array $sort
     * @return array
     */
    public function ksortMultibyte(array &$sort)
    {
        if (empty($sort)) {
            return false;
        }
        $oldLocale = setlocale(LC_COLLATE, "0");
        $localeCode = $this->_locale->getLocaleCode();
        // use fallback locale if $localeCode is not available
        setlocale(LC_COLLATE,  $localeCode . '.UTF8', 'C.UTF-8', 'en_US.utf8');
        ksort($sort, SORT_LOCALE_STRING);
        setlocale(LC_COLLATE, $oldLocale);

        return $sort;
    }

    /**
     * Capitalize first letters and convert separators if needed
     *
     * @param string $str
     * @param string $sourceSeparator
     * @param string $destinationSeparator
     * @return string
     */
    public static function upperCaseWords($str, $sourceSeparator = '_', $destinationSeparator = '_')
    {
        return str_replace(' ', $destinationSeparator, ucwords(str_replace($sourceSeparator, ' ', $str)));
    }

    /**
     * Builds namespace + classname out of the parts array
     *
     * Split every part into pieces by _ and \ and uppercase every piece
     * Then join them back using \
     *
     * @param $parts
     * @return string
     */
    public static function buildClassName($parts)
    {
        $separator = \Magento\Autoload\IncludePath::NS_SEPARATOR;

        $string = join($separator, $parts);
        $string = str_replace('_', $separator, $string);
        $className = \Magento\Core\Helper\String::upperCaseWords($string, $separator, $separator);

        return $className;
    }
}
