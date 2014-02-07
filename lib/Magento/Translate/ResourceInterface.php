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
     * @param int $scope
     * @param string $locale
     * @return array
     */
    public function getTranslationArray($scope = null, $locale = null);
}
