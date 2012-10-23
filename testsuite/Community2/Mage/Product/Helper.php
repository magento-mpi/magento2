<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     selenium
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
class Community2_Mage_Product_Helper extends Core_Mage_Product_Helper
{
    /**
     * Delete all Custom Options
     *
     * @return void
     */
    public function deleteCustomOptions()
    {
        $this->openTab('custom_options');
        $fieldSetXpath = $this->_getControlXpath('fieldset', 'custom_option_set');
        $optionsQty = $this->getXpathCount($fieldSetXpath);
        $optionId ='';
        While ($optionsQty > 0) {
            $elementId = $this->getAttribute($fieldSetXpath . "[{$optionsQty}]/@id");
            $elementId = explode('_', $elementId);
            foreach ($elementId as $id) {
                if (is_numeric($id)) {
                    $optionId = $id;
                }
            }
            $this->addParameter('optionId', $optionId);
            $this->clickButton('delete_option', false);
            $optionsQty--;
        }
    }
    /**
     * Get Custom Option Id By Title
     *
     * @param string
     * @return integer
     */
    public function getCustomOptionId($optionTitle)
    {
        $optionId = '';
        $fieldSetXpath = $this->_getControlXpath('fieldset', 'custom_option_set');
        if ($this->isElementPresent($fieldSetXpath . "//input[@value='{$optionTitle}']")) {
            $elementId = $this->getAttribute($fieldSetXpath . "//input[@value='{$optionTitle}'][1]@id");
            $elementId = explode('_', $elementId);
            foreach ($elementId as $id) {
                if (is_numeric($id)) {
                    $optionId = $id;
                }
            }
        }
        return $optionId;
    }
    /**
     * Check if product is present in products grid
     *
     * @param array $productData
     * @return bool
     */
    public function isProductPresentInGrid($productData)
    {
        $data = array('product_sku' => $productData['product_sku']);
        $this->_prepareDataForSearch($data);
        $xpathTR = $this->search($data, 'product_grid');
        if (!is_null($xpathTR)) {
            return true;
        } else {
            return false;
        }
    }

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
            $this->fillFieldset($dataForWebsitesTab, 'add_product');
        }
        else {
            $this->fail('data for websites tab is absent');
        }
    }
    /**
     * Fill Product Tab
     *
     * @param array $productData
     * @param string $tabName Value - general|prices|meta_information|images|recurring_profile
     * |design|gift_options|inventory|websites|categories|related|up_sells
     * |cross_sells|custom_options|bundle_items|associated|downloadable_information
     *
     * @return bool
     */
    public function fillProductTab(array $productData, $tabName = 'general')
    {
        $tabData = array();
        $needFilling = false;

        foreach ($productData as $key => $value) {
            if (preg_match('/^' . $tabName . '/', $key)) {
                $tabData[$key] = $value;
            }
        }

        if ($tabData) {
            $needFilling = true;
        }

        $tabXpath = $this->_getControlXpath('tab', $tabName);
        if ($tabName == 'websites' && !$this->isElementPresent($tabXpath)) {
            $needFilling = false;
        }

        if (!$needFilling) {
            return true;
        }

        $this->openTab($tabName);

        switch ($tabName) {
            case 'prices':
                $arrayKey = 'prices_tier_price_data';
                if (array_key_exists($arrayKey, $tabData) && is_array($tabData[$arrayKey])) {
                    foreach ($tabData[$arrayKey] as $value) {
                        $this->addTierPrice($value);
                    }
                }
                $arrayKey1 = 'prices_group_price_data';
                if (array_key_exists($arrayKey1, $tabData) && is_array($tabData[$arrayKey1])) {
                    foreach ($tabData[$arrayKey1] as $value) {
                        $this->addGroupPrice($value);
                     }
                }
                $this->fillForm($tabData, 'prices');
                $this->fillUserAttributesOnTab($tabData, $tabName);
                break;
            case 'websites':
                $websites = explode(',', $tabData[$tabName]);
                $websites = array_map('trim', $websites);
                foreach ($websites as $value) {
                    $this->selectWebsite($value);
                }
                break;
            case 'categories':
                $categories = explode(',', $tabData[$tabName]);
                $categories = array_map('trim', $categories);
                foreach ($categories as $value) {
                    $this->categoryHelper()->selectCategory($value);
                }
                break;
            case 'related':
            case 'up_sells':
            case 'cross_sells':
                $arrayKey = $tabName . '_data';
                if (array_key_exists($arrayKey, $tabData) && is_array($tabData[$arrayKey])) {
                    foreach ($tabData[$arrayKey] as $value) {
                        $this->assignProduct($value, $tabName);
                    }
                }
                break;
            case 'custom_options':
                $arrayKey = $tabName . '_data';
                if (array_key_exists($arrayKey, $tabData) && is_array($tabData[$arrayKey])) {
                    foreach ($tabData[$arrayKey] as $value) {
                        $this->addCustomOption($value);
                    }
                }
                break;
            case 'bundle_items':
                $arrayKey = $tabName . '_data';
                if (array_key_exists($arrayKey, $tabData) && is_array($tabData[$arrayKey])) {
                    if (array_key_exists('ship_bundle_items', $tabData[$arrayKey])) {
                        $array['ship_bundle_items'] = $tabData[$arrayKey]['ship_bundle_items'];
                        $this->fillForm($array, 'bundle_items');
                    }
                    foreach ($tabData[$arrayKey] as $value) {
                        if (is_array($value)) {
                            $this->addBundleOption($value);
                        }
                    }
                }
                break;
            case 'associated':
                $arrayKey = $tabName . '_grouped_data';
                $arrayKey1 = $tabName . '_configurable_data';
                if (array_key_exists($arrayKey, $tabData) && is_array($tabData[$arrayKey])) {
                    foreach ($tabData[$arrayKey] as $value) {
                        $this->assignProduct($value, $tabName);
                    }
                } elseif (array_key_exists($arrayKey1, $tabData) && is_array($tabData[$arrayKey1])) {
                    $attributeTitle = (isset($productData['configurable_attribute_title']))
                        ? $productData['configurable_attribute_title']
                        : null;
                    if (!$attributeTitle) {
                        $this->fail('Attribute Title for configurable product is not set');
                    }
                    $this->addParameter('attributeTitle', $attributeTitle);
                    $this->fillForm($tabData[$arrayKey1], $tabName);
                    foreach ($tabData[$arrayKey1] as $value) {
                        if (is_array($value)) {
                            $this->assignProduct($value, $tabName, $attributeTitle);
                        }
                    }
                }
                break;
            case 'downloadable_information':
                $arrayKey = $tabName . '_data';
                if (array_key_exists($arrayKey, $tabData) && is_array($tabData[$arrayKey])) {
                    foreach ($tabData[$arrayKey] as $key => $value) {
                        if (preg_match('/^downloadable_sample_/', $key) && is_array($value)) {
                            $this->addDownloadableOption($value, 'sample');
                        }
                        if (preg_match('/^downloadable_link_/', $key) && is_array($value)) {
                            $this->addDownloadableOption($value, 'link');
                        }
                    }
                }
                $this->fillForm($tabData[$arrayKey], $tabName);
                break;
            default:
                $this->fillForm($tabData, $tabName);
                $this->fillUserAttributesOnTab($tabData, $tabName);
                break;
        }
        return true;
    }

    /**
     * Add Group Price
     *
     * @param array $groupPriceData
     */
    public function addGroupPrice(array $groupPriceData)
    {
        $rowNumber = $this->getXpathCount($this->_getControlXpath('fieldset', 'group_price_row'));
        $this->addParameter('groupPriceId', $rowNumber);
        $this->clickButton('add_group_price', false);
        $this->fillForm($groupPriceData, 'prices');
    }
}