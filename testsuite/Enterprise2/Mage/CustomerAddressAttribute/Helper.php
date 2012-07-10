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
class Enterprise2_Mage_CustomerAddressAttribute_Helper extends Mage_Selenium_TestCase
{
    /**
     * Action_helper method for Create Attribute
     *
     * Preconditions: 'Manage Attributes' page is opened.
     * @param array $attrData Array which contains DataSet for filling of the current form
     */
    public function createAttribute($attrData)
    {
        if (is_string($attrData)) {
            $elements = explode('/', $attrData);
            $fileName = (count($elements) > 1) ? array_shift($elements) : '';
            $attrData = $this->loadDataSet($fileName, implode('/', $elements));
        }
        $this->clickButton('add_new_attribute');
        foreach ($attrData as $tabId => $data) {
            $this->fillTab($data, $tabId);
        }
//        $this->storeViewTitles($attrData);
        $this->saveForm('save_attribute');
    }

    /**
     * Open Customer Address Attribute.
     *
     * Preconditions: 'Manage Attributes' page is opened.
     * @param array $searchData
     */
    public function openAttribute($searchData)
    {
        $this->_prepareDataForSearch($searchData);
        $xpathTR = $this->search($searchData, 'attributes_grid');
        $this->assertNotNull($xpathTR, 'Attribute is not found');
        $cellId = $this->getColumnIdByName('Attribute Code');
        $this->addParameter('attribute_code', $this->getText($xpathTR . '//td[' . $cellId . ']'));
        $this->addParameter('id', $this->defineIdFromTitle($xpathTR));
        $this->click($xpathTR . '//td[' . $cellId . ']');
        $this->waitForPageToLoad($this->_browserTimeoutPeriod);
        $this->validatePage();
    }

    /**
     * Verify all data in saved Attribute.
     *
     * Preconditions: Attribute page is opened.
     * @param array $attrData
     */
    public function verifyAttribute($attrData)
    {
        $this->assertTrue($this->verifyForm($attrData, 'properties'), $this->getParsedMessages());
        $this->openTab('manage_labels_options');
        $this->storeViewTitles($attrData, 'manage_titles', 'verify');
    }

    /**
     * Fill or Verify Titles for different Store View
     *
     * @param array $attrData
     * @param string $fieldsetName
     * @param string $action
     */
    public function storeViewTitles($attrData, $fieldsetName = 'manage_titles', $action = 'fill')
    {
        $name = 'store_view_titles';
        if (isset($attrData['admin_title'])) {
            $attrData[$name]['Admin'] = $attrData['admin_title'];
        }
        if (array_key_exists($name, $attrData)
            && is_array($attrData[$name])
            && $attrData[$name] != '%noValue%'
        ) {
            $fieldSetXpath = $this->_getControlXpath('fieldset', $fieldsetName);
            $qtyStore = $this->getXpathCount($fieldSetXpath . '//th');
            foreach ($attrData[$name] as $storeViewName => $storeViewValue) {
                $number = -1;
                for ($i = 1; $i <= $qtyStore; $i++) {
                    if ($this->getText($fieldSetXpath . '//th[' . $i . ']') == $storeViewName) {
                        $number = $i;
                        break;
                    }
                }
                if ($number != -1) {
                    $this->addParameter('storeViewNumber', $number);
                    $fieldSet = $this->_findUimapElement('fieldset', $fieldsetName);
                    $fieldXpath = $this->_getControlXpath('field', 'titles_by_store_name', $fieldSet);
                    switch ($action) {
                        case 'fill':
                            if ($storeViewValue != '%noValue%') {
                                $this->type($fieldXpath, $storeViewValue);
                            }
                            break;
                        case 'verify':
                            $actualText = $this->getValue($fieldXpath);
                            $var = array_flip(get_html_translation_table());
                            $actualText = strtr($actualText, $var);
                            $this->assertEquals($storeViewValue, $actualText, 'Stored data not equals to specified');
                            break;
                    }
                } else {
                    $this->fail('Cannot find specified Store View with name \'' . $storeViewName . '\'');
                }
            }
        }
    }
    /**
     * Define Attribute Id
     *
     * @param array $searchData
     * @return int
     */
    public function defineAttributeId(array $searchData)
    {
        $this->navigate('manage_customer_address_attributes');
        $attrXpath = $this->search($searchData);
        $this->assertNotEquals(null, $attrXpath);

        return $this->defineIdFromTitle($attrXpath);
    }
}