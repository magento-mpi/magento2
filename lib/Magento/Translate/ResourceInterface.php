<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Translate;

interface ResourceInterface
{
    /**
     * Retrieve translation array for store / locale code
     *
     * @param int $storeId
     * @param string|Zend_Locale $locale
     * @return array
     */
    public function getTranslationArray($storeId = null, $locale = null);
}
