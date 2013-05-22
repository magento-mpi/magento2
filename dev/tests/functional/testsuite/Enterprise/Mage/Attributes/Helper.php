<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Attributes
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
class Enterprise_Mage_Attributes_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * Action_helper method for Create Customer Attribute
     *
     * Preconditions: 'Manage Customer Attributes' page is opened.
     *
     * @param array $attrData Array which contains DataSet for filling of the current form
     */
    public function createAttribute($attrData)
    {
        $this->clickButton('add_new_attribute');
        $this->fillTabs($attrData);
        $this->saveForm('save_attribute');
    }

    /**
     * Filling tabs
     *
     * @param string|array $attrData
     */
    public function fillTabs($attrData)
    {
        $attrData = $this->fixtureDataToArray($attrData);
        $optionsTab = (isset($attrData['manage_labels_options'])) ? $attrData['manage_labels_options'] : array();
        if (isset($attrData['properties'])) {
            $this->fillTab($attrData['properties'], 'properties');
        }
        $this->openTab('manage_labels_options');
        $this->productAttributeHelper()->storeViewTitles($optionsTab);
        $this->productAttributeHelper()->attributeOptions($optionsTab);
    }

    /**
     * Open Customer Attributes.
     * Preconditions: 'Manage Customer Attributes' page is opened.
     *
     * @param array $searchData
     */
    public function openAttribute($searchData)
    {
        $searchData = $this->_prepareDataForSearch($searchData);
        $xpathTR = $this->search($searchData, 'attributes_grid');
        $this->assertNotNull($xpathTR, 'Attribute is not found with data: ' . print_r($searchData, true));
        $attributeRowElement = $this->getElement($xpathTR);
        $attributeUrl = $attributeRowElement->attribute('title');
        //Define and add parameters for new page
        $cellId = $this->getColumnIdByName('Attribute Label');
        $cellElement = $this->getChildElement($attributeRowElement, 'td[' . $cellId . ']');
        $this->addParameter('elementTitle', trim($cellElement->text()));
        $this->addParameter('id', $this->defineParameterFromUrl('attribute_id', $attributeUrl));
        //Open attribute
        $this->url($attributeUrl);
        $this->validatePage();
    }
}