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
     * Action_helper method for Create Customer/Rma Attribute
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
     * Filling tabs for customer/rma attribute
     *
     * @param string|array $attrData
     */
    public function fillTabs($attrData)
    {
        $attrData = $this->fixtureDataToArray($attrData);
        $propertiesTab = (isset($attrData['properties'])) ? $attrData['properties'] : $attrData;
        $optionsTab = (isset($attrData['manage_labels_options'])) ? $attrData['manage_labels_options'] : array();

        $this->fillTab($propertiesTab, 'properties');
        if ($optionsTab) {
            $this->openTab('manage_labels_options');
            $this->productAttributeHelper()->storeViewTitles($optionsTab);
            $this->productAttributeHelper()->fillManageOptions($optionsTab);
        }
    }

    /**
     * Open Customer Attributes.
     * Preconditions: 'Manage Customer Attributes' page is opened.
     *
     * @param array $searchData
     */
    public function openAttribute($searchData)
    {
        $this->_prepareDataForSearch($searchData);
        $xpathTR = $this->search($searchData, 'attributes_grid');
        $this->assertNotNull($xpathTR, 'Attribute is not found');
        $cellId = $this->getColumnIdByName('Attribute Label');
        $this->addParameter('elementTitle', $this->getElement($xpathTR . '//td[' . $cellId . ']')->text());
        $this->addParameter('id', $this->defineIdFromTitle($xpathTR));
        $this->getElement($xpathTR . '//td[' . $cellId . ']')->click();
        $this->waitForPageToLoad();
        $this->validatePage();
    }

    /**
     * Verify data for customer/rma attribute
     *
     * @param array $attributeData
     */
    public function verifyAttribute(array $attributeData)
    {
        $this->assertTrue($this->verifyForm($attributeData, 'properties'), $this->getParsedMessages());
        $this->verifyForm($attributeData, 'properties');
        if (isset($attributeData['manage_labels_options'])) {
            $this->openTab('manage_labels_options');
            $this->productAttributeHelper()->storeViewTitles($attributeData, 'manage_titles', 'verify');
            $this->productAttributeHelper()->verifyManageOptions($attributeData);
        }
    }
}