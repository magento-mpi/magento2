<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
# @category    Magento
# @package     Mage_GiftRegistry
# @subpackage  helper
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Helper class
 *
 * @package     Mage_CmsWidget
 * @subpackage  functional_tests
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
}