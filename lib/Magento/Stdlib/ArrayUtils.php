<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    \Magento\Stdlib
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Stdlib;

/**
 * Class ArrayUtils
 *
 * @package Magento\Stdlib
 */
class ArrayUtils
{
    /**
     * Sorts array with multibyte string keys
     *
     * @param  array $sort
     * @param  string $locale
     * @return array|bool
     */
    public function ksortMultibyte(array &$sort, $locale)
    {
        if (empty($sort)) {
            return false;
        }
        $oldLocale = setlocale(LC_COLLATE, "0");
        // use fallback locale if $localeCode is not available

        if (strpos($locale, '.UTF8') === false) {
            $locale .= '.UTF8';
        }

        setlocale(LC_COLLATE,  $locale, 'C.UTF-8', 'en_US.utf8');
        ksort($sort, SORT_LOCALE_STRING);
        setlocale(LC_COLLATE, $oldLocale);

        return $sort;
    }
}
