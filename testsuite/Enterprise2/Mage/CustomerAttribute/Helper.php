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
 * @category    tests
 * @package     selenium
 * @subpackage  tests
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise2_Mage_CustomerAttribute_Helper extends Mage_Selenium_TestCase
{
    /**
     * Action_helper method for Create Customer Attribute
     *
     * Preconditions: 'Manage Customer Attributes' page is opened.
     *
     * @param array $attrData Array which contains DataSet for filling  of the current form
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
        if (is_string($attrData)) {
            $elements = explode('/', $attrData);
            $fileName = (count($elements) > 1) ? array_shift($elements) : '';
            $attrData = $this->loadDataSet($fileName, implode('/', $elements));
        }
        $propertiesTab = (isset($attrData['properties']))? $attrData['properties']: array();
        $optionsTab = (isset($attrData['manage_labels_options']))? $attrData['manage_labels_options']: array();

        $this->fillTab($propertiesTab, 'properties');
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
        $this->_prepareDataForSearch($searchData);
        $xpathTR = $this->search($searchData, 'attributes_grid');
        $this->assertNotNull($xpathTR, 'Attribute is not found');
        $cellId = $this->getColumnIdByName('Attribute Label');
        $this->addParameter('attribute_code', $this->getText($xpathTR . '//td[' . $cellId . ']'));
        $this->addParameter('id', $this->defineIdFromTitle($xpathTR));
        $this->click($xpathTR . '//td[' . $cellId . ']');
        $this->waitForPageToLoad($this->_browserTimeoutPeriod);
        $this->validatePage();
    }
}