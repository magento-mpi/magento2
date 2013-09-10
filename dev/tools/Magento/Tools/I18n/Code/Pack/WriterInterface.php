<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\I18n\Code\Pack;

use Magento\Tools\I18n\Code\Dictionary;
use Magento\Tools\I18n\Code\Locale;

/**
 * Pack writer interface
 */
interface WriterInterface
{
    /**#@+
     * Save pack modes
     */
    const MODE_REPLACE = 'replace';
    const MODE_MERGE = 'merge';
    /**#@-*/

    /**
     * Write dictionary data to language pack
     *
     * @param \Magento\Tools\I18n\Code\Dictionary $dictionary
     * @param string $packPath
     * @param \Magento\Tools\I18n\Code\Locale $locale
     * @param string $mode One of const of WriterInterface::MODE_
     */
    public function write(Dictionary $dictionary, $packPath, Locale $locale, $mode);
}
