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
     * Configurable product creation tests
     *
     * @package     selenium
     * @subpackage  tests
     * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     */
class Saas_Mage_Product_Create_ConfigurableTest extends Core_Mage_Product_Create_ConfigurableTest
{
    /**
     * <p>Creating Configurable product with Downloadable product</p>
     * <p>Override original testcase. Downloadable product is not available in Saas</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3363
     */
    public function configurableWithDownloadableProduct()
    {
        $this->markTestIncomplete('Functionality is absent in Magento Go.');
    }
}