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
 * Edit Gift Registry in Backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise2_Mage_GiftRegistry_EditTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Navigate to Manage Gift Registry</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_gift_registry');
    }

    /**
     * <p>Edit created Gift Registry</p>
     * <p>Preconditions:</p>
     * <p>Gift Registry created</p>
     * <p>Steps:</p>
     * <p>1. Open just created Gift Registry.</p>
     * <p>2. Navigate to "Code", "Label" fields.</p>
     * <p>3. Retype information in the fields.</p>
     * <p>4. Press "Save" button.</p>
     * <p>Expected Result:</p>
     * <p>Gift Registry is saved, changes are applied.</p>
     *
     * @test
     * @TestLinkId TL-MAGE-6223
     */
    public function editGiftRegistry()
    {
        //Data
        $giftRegistryData = $this->loadDataSet('GiftRegistry', 'new_gift_registry');
        $searchGiftRegistry = $this->loadDataSet('GiftRegistry', 'search_gift_registry',
            array('filter_label' => $giftRegistryData['general']['label']));
        $editGiftRegistryData = $this->loadDataSet('GiftRegistry', 'edit_gift_registry');
        $searchGiftRegistryEdited = $this->loadDataSet('GiftRegistry', 'search_gift_registry',
            array('filter_label' => $editGiftRegistryData['label']));
        //Steps
        $this->giftRegistryHelper()->createGiftRegistry($giftRegistryData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_gift_registry');
        //Steps
        $this->giftRegistryHelper()->openGiftRegistry($searchGiftRegistry);
        $this->fillFieldset($editGiftRegistryData, 'general_info');
        $this->saveForm('save_gift_registry');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_gift_registry');
        //Steps
        $this->giftRegistryHelper()->openGiftRegistry($searchGiftRegistryEdited);
        //Verifying
        $this->giftRegistryHelper()->verifyGiftRegistry($editGiftRegistryData);
    }
}