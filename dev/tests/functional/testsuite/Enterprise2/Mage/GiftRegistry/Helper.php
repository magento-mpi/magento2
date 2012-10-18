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
class Enterprise2_Mage_GiftRegistry_Helper extends Mage_Selenium_TestCase
{
    /**
     * Add Attribute
     *
     * @param array $attData
     */
    public function addAttributes(array $attData)
    {
        $fieldSetXpath = $this->_getControlXpath('fieldset', 'attributes_set');
        $attributeId = $this->getXpathCount($fieldSetXpath) + 1;
        $this->addParameter('attributeId', $attributeId);
        $this->clickButton('add_attribute', false);
        $this->fillFieldSet($attData, 'attributes_set');
        foreach ($attData as $optionKey => $optionValue) {
            if (preg_match('/^attributes_option/', $optionKey) && is_array($optionValue)) {
                $attributeId = $this->getXpathCount($fieldSetXpath . "//tr[contains(@id,'attribute_')][not(@style)]");
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
        $Id = $this->getColumnIdByName('Label');
        $this->addParameter('elementLabel', $this->getText($xpathGR . '//td[' . $Id . ']'));
        $this->addParameter('id', $this->defineIdFromTitle($xpathGR));
        $this->click($xpathGR);
        $this->waitForPageToLoad($this->_browserTimeoutPeriod);
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
        if (is_string($giftRegistryData)) {
            $elements = explode('/', $giftRegistryData);
            $fileName = (count($elements) > 1) ? array_shift($elements) : '';
            $giftRegistryData = $this->loadDataSet($fileName, implode('/', $elements));
        }
        $this->verifyForm($giftRegistryData);
        $this->assertEmptyVerificationErrors();
    }
}
