<?php

/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_GiftRegistry
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Delete Gift Registry in Backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_GiftRegistry_DeleteTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Log in to Backend.</p>
     * <p>Navigate to Customers -> Gift Registry./p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_gift_registry');
    }

    /**
     * <p>Delete created Gift Registry</p>
     *
     * @test
     * @TestLinkId TL-MAGE-6215
     */
    public function deleteSingleGiftRegistry()
    {
        //Data
        $giftRegistryData = $this->loadDataSet('GiftRegistry', 'new_gift_registry');
        $searchGiftRegistry = $this->loadDataSet('GiftRegistry', 'search_gift_registry',
            array('filter_label' => $giftRegistryData['general']['label']));
        //Steps
        $this->navigate('manage_gift_registry');
        $this->giftRegistryHelper()->createGiftRegistry($giftRegistryData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_gift_registry');
        //Steps
        $this->giftRegistryHelper()->deleteGiftRegistry($searchGiftRegistry);
        //Verifying
        $this->assertMessagePresent('success', 'success_deleted_gift_registry');
    }
}