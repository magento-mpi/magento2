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
        $attributeId = count($this->getControlElements('fieldset', 'attributes_set')) + 1;
        $this->addParameter('attributeId', $attributeId);
        $this->clickButton('add_attribute', false);
        $this->fillFieldSet($attData, 'attributes_set');
        foreach ($attData as $optionKey => $optionValue) {
            if (preg_match('/^attributes_option/', $optionKey) && is_array($optionValue)) {
                $attributeId = count($this->getElements($fieldSetXpath .
                    "//tr[contains(@id,'attribute_')][not(@style)]"));
                $this->addParameter('optionId', $attributeId);
                $this->clickButton('add_option', false);
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
     * @param array $searchGiftRegistry
     */
    public function openGiftRegistry(array $searchGiftRegistry)
    {
        $xpathGR = $this->search($searchGiftRegistry, 'giftRegistryGrid');
        $this->assertNotEquals(null, $xpathGR, 'Gift Registry is not found');
        $columnId = $this->getColumnIdByName('Label');
        $this->addParameter('elementLabel', $this->getElement($xpathGR . '//td[' . $columnId . ']')->text());
        $this->addParameter('id', $this->defineIdFromTitle($xpathGR));
        $this->getElement($xpathGR)->click();
        $this->waitForPageToLoad();
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
        $giftRegistryData = $this->testDataToArray($giftRegistryData);
        $this->verifyForm($giftRegistryData);
        $this->assertEmptyVerificationErrors();
    }
}
