<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Product
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Duplicate product tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Saas_Mage_Product_DuplicateTest extends Core_Mage_Product_DuplicateTest
{
    /**
     * <p>Creating duplicated downloadable product</p>
     * <p>Override original testcase. Downloadable product is not available in Saas</p>
     *
     * @param array $attrData
     * @param array $assignData
     * @param string $linksSeparately
     * @param string|float $linkPrice
     */
    public function duplicateDownloadable($linksSeparately = null, $linkPrice = null, $attrData = null,
                                          $assignData = null)
    {
        $this->markTestIncomplete('Functionality is absent in Magento Go.');
    }
}
