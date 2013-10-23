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
     * @var \Magento\Stdlib\String
     */
    protected $string;

    /**
     * @param \Magento\Core\Helper\Context $context
     * @param \Magento\Core\Model\Locale $locale
     * @param \Magento\Stdlib\String $string
     */
    public function __construct(
        \Magento\Core\Helper\Context $context,
        \Magento\Core\Model\Locale $locale,
        \Magento\Stdlib\String $string
    ) {
        parent::__construct($context);
        $this->_locale = $locale;
        $this->string = $string;
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
        $str = $this->string->split($str, $length);
        $newStr = '';
        foreach ($str as $part) {
            if ($this->string->strlen($part) >= $length) {
                $lastDelimiter = $this->string->strpos($this->string->strrev($part), $needle);
                $tmpNewStr = $this->string->substr($this->string->strrev($part), 0, $lastDelimiter)
                    . $insert . $this->string->substr($this->string->strrev($part), $lastDelimiter);
                $newStr .= $this->string->strrev($tmpNewStr);
            } else {
                $newStr .= $part;
            }
        }
        return $newStr;
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
        $className = \Magento\Stdlib\String::upperCaseWords($string, $separator, $separator);

        return $className;
    }
}
