<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     \Magento\Stdlib
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Stdlib;

/**
 * Magento methods to work with string
 */
class String
{
    /**
     * @var StringIconv
     */
    protected $stringIconv;

    /**
     * @param StringIconv $stringIconv
     */
    public function __construct(StringIconv $stringIconv)
    {
        $this->stringIconv = $stringIconv;
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
     * Binary-safe variant of strSplit()
     * + option not to break words
     * + option to trim spaces (between each word)
     * + option to set character(s) (pcre pattern) to be considered as words separator
     *
     * @param string $value
     * @param int $length
     * @param bool $keepWords
     * @param bool $trim
     * @param string $wordSeparatorRegex
     * @return array
     */
    public function split($value, $length = 1, $keepWords = false, $trim = false, $wordSeparatorRegex = '\s')
    {
        $result = array();
        $strLen = $this->stringIconv->strlen($value);
        if (!$strLen || !is_int($length) || $length <= 0) {
            return $result;
        }
        if ($trim) {
            $value = trim(preg_replace('/\s{2,}/siu', ' ', $value));
        }
        // do a usual str_split, but safe for our encoding
        if (!$keepWords || $length < 2) {
            for ($offset = 0; $offset < $strLen; $offset += $length) {
                $result[] = $this->stringIconv->substr($value, $offset, $length);
            }
        } else {
            // split smartly, keeping words
            $split = preg_split('/(' . $wordSeparatorRegex . '+)/siu', $value, null, PREG_SPLIT_DELIM_CAPTURE);
            $index = 0;
            $space = '';
            $spaceLen = 0;
            foreach ($split as $key => $part) {
                if ($trim) {
                    // ignore spaces (even keys)
                    if ($key % 2) {
                        continue;
                    }
                    $space = ' ';
                    $spaceLen = 1;
                }
                if (empty($result[$index])) {
                    $currentLength = 0;
                    $result[$index] = '';
                    $space = '';
                    $spaceLen = 0;
                } else {
                    $currentLength = $this->stringIconv->strlen($result[$index]);
                }
                $partLength = $this->stringIconv->strlen($part);
                // add part to current last element
                if (($currentLength + $spaceLen + $partLength) <= $length) {
                    $result[$index] .= $space . $part;
                } elseif ($partLength <= $length) {
                    // add part to new element
                    $index++;
                    $result[$index] = $part;
                } else {
                    // break too long part recursively
                    foreach ($this->split($part, $length, false, $trim, $wordSeparatorRegex) as $subPart) {
                        $index++;
                        $result[$index] = $subPart;
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
}
