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
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_GiftRegistry_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * Add Attribute
     *
     * @param array $attData
     */
    public function addAttributes(array $attData)
    {
        $fieldSetXpath = $this->_getControlXpath('fieldset', 'attributes_set');
        $this->clickButton('add_attribute', false);
        $attributeId = $this->getControlCount('fieldset', 'attributes_set');
        $this->addParameter('attributeId', $attributeId);
        $this->fillFieldSet($attData, 'attributes_set');
        foreach ($attData as $optionKey => $optionValue) {
            if (preg_match('/^attributes_option/', $optionKey) && is_array($optionValue)) {
                $this->clickButton('add_option', false);
                $attributeId = count($this->getElements($fieldSetXpath .
                    "//tr[contains(@id,'attribute_')][not(@style)]"));
                $this->addParameter('optionId', $attributeId-1);
                $this->fillFieldSet($optionValue, 'attributes_set');
            }
        }
    }

    /**
     * Create Gift Registry.
     *
     * @param $giftRegistryData
     */
    public function createGiftRegistry($giftRegistryData)
    {
        $general = (isset($giftRegistryData['general'])) ? $giftRegistryData['general'] : array();
        $attributes = (isset($giftRegistryData['attributes'])) ? $giftRegistryData['attributes'] : array();
        $this->clickButton('add_new_gift_registry_type');
        $this->fillFieldset($general, 'general_info');
        if ($attributes) {
            $this->openTab('attributes');
            foreach ($attributes as $attribute) {
                $this->addAttributes($attribute);
            }
        }
        $this->saveForm('save_gift_registry');
    }

    /**
     * Opens Gift Registry
     *
     * @param array $searchData
     */
    public function openGiftRegistry(array $searchData)
    {
        //Search Gift Registry
        $searchData = $this->_prepareDataForSearch($searchData);
        $registryLocator = $this->search($searchData, 'giftRegistryGrid');
        $this->assertNotNull($registryLocator, 'Gift Registry is not found with data: ' . print_r($searchData, true));
        $registryRowElement = $this->getElement($registryLocator);
        $registryUrl = $registryRowElement->attribute('title');
        //Define and add parameters for new page
        $cellId = $this->getColumnIdByName('Label');
        $cellElement = $this->getChildElement($registryRowElement, 'td[' . $cellId . ']');
        $this->addParameter('elementTitle', trim($cellElement->text()));
        $this->addParameter('id', $this->defineIdFromUrl($registryUrl));
        //Open Gift Registry
        $this->url($registryUrl);
        $this->validatePage();
    }

    /**
     * Deletes Gift Registry
     *
     * @param array $searchGiftRegistry
     */
    public function deleteGiftRegistry(array $searchGiftRegistry)
    {
        $this->openGiftRegistry($searchGiftRegistry);
        $this->clickButtonAndConfirm('delete', 'confirmation_for_delete');
    }

    /**
     * Verify entered data
     *
     * @param string $giftRegistryData
     */
    public function verifyGiftRegistry($giftRegistryData)
    {
        $giftRegistryData = $this->fixtureDataToArray($giftRegistryData);
        $this->verifyForm($giftRegistryData);
        $this->assertEmptyVerificationErrors();
    }
}
