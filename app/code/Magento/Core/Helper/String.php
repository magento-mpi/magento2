<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Core data helper
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Core\Helper;

class String extends \Magento\Core\Helper\AbstractHelper
{
    /**
     * @var \Magento\Core\Model\LocaleInterface
     */
    protected $_locale;

    /**
     * @var \Magento\Stdlib\StringIconv
     */
    protected $_stringIconv;

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
        $this->_stringIconv = $stringIconv;
    }


    /**
     * Truncate a string to a certain length if necessary, appending the $etc string.
     * $remainder will contain the string that has been replaced with $etc.
     *
     * @param string $string
     * @param int $length
     * @param string $etc
     * @param string &$remainder
     * @param bool $breakWords
     * @return string
     */
    public function truncate($string, $length = 80, $etc = '...', &$remainder = '', $breakWords = true)
    {
        $remainder = '';
        if (0 == $length) {
            return '';
        }

        $originalLength = $this->_stringIconv->strlen($string);
        if ($originalLength > $length) {
            $length -= $this->_stringIconv->strlen($etc);
            if ($length <= 0) {
                return '';
            }
            $preparedString = $string;
            $preparedlength = $length;
            if (!$breakWords) {
                $preparedString = preg_replace(
                    '/\s+?(\S+)?$/u', '', $this->_stringIconv->substr($string, 0, $length + 1)
                );
                $preparedlength = $this->_stringIconv->strlen($preparedString);
            }
            $remainder = $this->_stringIconv->substr($string, $preparedlength, $originalLength);
            return $this->_stringIconv->substr($preparedString, 0, $length) . $etc;
        }

        return $string;
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
            if ($this->_stringIconv->strlen($part) >= $length) {
                $lastDelimetr = $this->_stringIconv->strpos($this->_stringIconv->strrev($part), $needle);
                $tmpNewStr = '';
                $tmpNewStr = $this->_stringIconv->substr($this->_stringIconv->strrev($part), 0, $lastDelimetr)
                    . $insert . $this->_stringIconv->substr($this->_stringIconv->strrev($part), $lastDelimetr);
                $newStr .= $this->_stringIconv->strrev($tmpNewStr);
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
        $strlen = $this->_stringIconv->strlen($str);
        if ((!$strlen) || (!is_int($length)) || ($length <= 0)) {
            return $result;
        }
        // trim
        if ($trim) {
            $str = trim(preg_replace('/\s{2,}/siu', ' ', $str));
        }
        // do a usual str_split, but safe for our encoding
        if ((!$keepWords) || ($length < 2)) {
            for ($offset = 0; $offset < $strlen; $offset += $length) {
                $result[] = $this->_stringIconv->substr($str, $offset, $length);
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
                    $currentLength = $this->_stringIconv->strlen($result[$i]);
                }
                $partLength = $this->_stringIconv->strlen($part);
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
        if ($count = count($result)) {
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

}
