<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_AdminUser
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community2_Mage_BatchUpdates_Products_Helper extends Mage_Selenium_TestCase
{
    /**
     * Fill in Product Settings tab
     *
     * @param array $dataForAttributesTab
     * @param array $dataForInventoryTab
     * @param array $dataForWebsitesTab
     */
    public function updateThroughMassAction($dataForAttributesTab, $dataForInventoryTab, $dataForWebsitesTab)
    {
        if(isset($dataForAttributesTab)) {
            $this->fillFieldset($dataForAttributesTab, 'attributes');
        }
        else {
            $this->fail('data for attributes tab is absent');
        }
        if(isset($dataForInventoryTab)) {
            $this->fillFieldset($dataForInventoryTab, 'inventory');
        }
        else {
            $this->fail('data for inventory tab is absent');
        }
        if(isset($dataForWebsitesTab)) {
            $this->fillFieldset($dataForWebsitesTab, 'websites');
        }
        else {
            $this->fail('data for websites tab is absent');
        }
    }
}