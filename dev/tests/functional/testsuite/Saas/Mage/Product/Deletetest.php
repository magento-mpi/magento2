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
     * Products deletion tests
     *
     * @package     selenium
     * @subpackage  tests
     * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     */
class Saas_Mage_Product_DeleteTest extends Core_Mage_Product_DeleteTest
{
    /**
     * <p>Override DataProvider to exclude downloadable product type</p>
     *
     * @return array
     */
    public function deleteSingleProductDataProvider()
    {
        return array(
            array('simple'),
            array('virtual'),
            array('grouped'),
            array('bundle')
        );
    }

    /**
     * Override DataProvider to exclude downloadable product type
     *
     * @return array
     */
    public function deleteAssociatedProductDataProvider()
    {
        return array(
            array('simple', 'grouped'),
            array('virtual', 'grouped'),
            array('simple', 'bundle'),
            array('virtual', 'bundle')
        );
    }
}