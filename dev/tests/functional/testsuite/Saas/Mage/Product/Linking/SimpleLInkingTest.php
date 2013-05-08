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
 * Test for related, up-sell and cross-sell products.
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Saas_Mage_Product_Linking_SimpleLinkingTest extends Core_Mage_Product_Linking_SimpleLinkingTest
{
    protected $_productTypes = array('configurable', 'bundle', 'grouped', 'simple', 'virtual');

    public function linkingTypeDataProvider()
    {
        return array(
            array('simple'),
            array('virtual'),
            array('bundle'),
            array('configurable'),
            array('grouped')
        );
    }
}