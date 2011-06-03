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
class ProductAttribute_Helper extends Mage_Selenium_TestCase
{

    /**
     * Action_helper method for Create Attribute
     *
     * Preconditions: 'Manage Attributes' page is opened.
     * @param array $attrData Array which contains DataSet for filling of the current form
     */
    public function createAttribute($attrData)
    {
        $this->clickButton('add_new_attribute');
        $this->fillForm($attrData, 'properties');
        $this->clickControl('tab', 'manage_lables_options', false);
        $this->fillForm($attrData, 'manage_lables_options');
        $this->attributeTiteles($attrData);
        $this->attributeOptions($attrData);
        $this->saveForm('save_attribute');
    }

    /**
     * Open Product Attribute.
     *
     * Preconditions: 'Manage Attributes' page is opened.
     * @param array $searchData
     */
    public function openAttribute($searchData)
    {
        $this->clickButton('reset_filter');
        $this->navigate('manage_attributes');
        $this->assertTrue($this->searchAndOpen($searchData), 'Attribute is not found');
    }

    /**
     * Verify all data in saved Attribute.
     *
     * Preconditions: Attribute page is opened.
     * @param array $attrData
     */
    public function verifyAttribute($attrData)
    {
        $this->assertTrue($this->verifyForm($attrData, 'properties'), $this->messages);
        $this->clickControl('tab', 'manage_lables_options', FALSE);
        $this->assertTrue($this->verifyForm($attrData, 'manage_lables_options'), $this->messages);
        $this->attributeTiteles($attrData, 'verify');
        $this->attributeOptions($attrData, 'verify');
    }

    /**
     * Create Attribute from product page.
     *
     * Preconditions: Product page is opened.
     * @param array $attrData
     */
    public function createAttributeOnGeneralTab($attrData)
    {
        // Defining and adding %fieldSetId% for Uimap pages.
        $page = $this->getCurrentLocationUimapPage();
        $fieldSet = $page->findFieldset('general');
        $id = explode('_', $this->getAttribute($fieldSet->getXPath() . '@id'));
        foreach ($id as $value) {
            if (is_numeric($value)) {
                $fieldSetId = $value;
                $this->addParameter('tabId', $fieldSetId);
                break;
            }
        }
        //Steps. Сlick 'Create New Attribute' button, select opened window.
        $this->clickButton('create_new_attribute', FALSE);
        $names = $this->getAllWindowNames();
        $this->waitForPopUp(end($names), '30000');
        $this->selectWindow("name=" . end($names));
        $this->fillForm($attrData, 'properties');
        $this->clickControl('tab', 'manage_lables_options', FALSE);
        $this->fillForm($attrData, 'manage_lables_options');
        $this->attributeTiteles($attrData);
        $this->attributeOptions($attrData);
        $this->saveForm('save_attribute');
    }

    /**
     * Fill or verify attribute titles by store view name
     *
     * Preconditions: Attribute page is opened on tab 'Manage Label / Options'.
     * @param array $attrData
     * @param string $action
     */
    public function attributeTiteles($attrData, $action = 'fill')
    {
        $dataArr = array();
        foreach ($attrData as $f_key => $d_value) {
            if (preg_match('/title/', $f_key) and is_array($attrData[$f_key])) {
                reset($attrData[$f_key]);
                $key = current($attrData[$f_key]);
                $value = next($attrData[$f_key]);
                $dataArr[$key] = $value;
            }
        }
        $this->fillOrVerifyFields($dataArr, 'title_by_store_name', $action);
    }

    /**
     * Fill or verify attribute options
     *
     * Preconditions: Attribute page is opened on tab 'Manage Label / Options'.
     * @param array $attrData
     * @param string $action
     */
    public function attributeOptions($attrData, $action = 'fill')
    {
        $page = $this->getCurrentLocationUimapPage();
        $fieldSet = $page->findFieldset('manage_options');
        $fieldSetXpath = $fieldSet->getXPath();
        if ($action == 'verify') {
            $option = $this->getXpathCount($fieldSetXpath . "//tr[contains(@class,'option-row')]");
            $num = 1;
        }
        foreach ($attrData as $f_key => $d_value) {
            if (preg_match('/option/', $f_key) and is_array($attrData[$f_key])) {
                if ($this->isElementPresent($fieldSetXpath)) {
                    $optionCount = $this->getXpathCount($fieldSetXpath .
                                    "//tr[contains(@class,'option-row')]");
                    switch ($action) {
                        case 'fill':
                            $this->addParameter('fieldOptionNumber', $optionCount);
                            $page->assignParams($this->_paramsHelper);
                            $this->clickButton('add_option', FALSE);
                            $this->fillForm($attrData[$f_key], 'manage_lables_options');
                            break;
                        case 'verify':
                            if ($option > 0) {
                                $fieldOptionNumber = $this->getAttribute($fieldSetXpath .
                                                "//tr[contains(@class,'option-row')][" .
                                                $num . "]//input[@class='input-radio']/@value");
                                $this->addParameter('fieldOptionNumber', $fieldOptionNumber);
                                $page->assignParams($this->_paramsHelper);
                                $this->assertTrue($this->verifyForm($attrData[$f_key],
                                                'manage_lables_options'), $this->messages);
                                $num++;
                                $option--;
                            }
                            break;
                    }

                    $dataArr = array();
                    foreach ($attrData[$f_key] as $k1 => $v2) {
                        if (is_array($attrData[$f_key][$k1])
                                and preg_match('/store_view_option_name/', $k1)) {
                            reset($attrData[$f_key][$k1]);
                            $key = current($attrData[$f_key][$k1]);
                            $value = next($attrData[$f_key][$k1]);
                            $dataArr[$key] = $value;
                        }
                    }
                    $this->fillOrVerifyFields($dataArr, 'option_name_by_store_name', $action);
                }
            }
        }
    }

    /**
     * Fill or Verify one title or option by store view name
     *
     * Preconditions: Attribute page is opened on tab 'Manage Label / Options'.
     * @param array $data
     * @param string $fieldName
     * @param string $action
     */
    public function fillOrVerifyFields(array $data, $fieldName, $action)
    {
        $page = $this->getCurrentLocationUimapPage();
        if ($fieldName == 'title_by_store_name') {
            $fieldSet = $page->findFieldset('manage_titles');
        } elseif ($fieldName == 'option_name_by_store_name') {
            $fieldSet = $page->findFieldset('manage_options');
        }

        $fieldSetXpath = $fieldSet->getXPath();
        $qtyStore = $this->getXpathCount($fieldSetXpath . '//th');
        foreach ($data as $k => $v) {
            $number = -1;
            for ($i = 1; $i <= $qtyStore; $i++) {
                if ($this->getText($fieldSetXpath . "//th[$i]") == $k) {
                    $number = $i;
                    break;
                }
            }
            if ($number != -1) {
                $this->addParameter('storeViewNumber', $number);
                $page->assignParams($this->_paramsHelper);
                $fieldXpath = $fieldSetXpath . $page->findField($fieldName);
                switch ($action) {
                    case 'fill':
                        $this->type($fieldXpath, $v);
                        break;
                    case 'verify':
                        $this->assertEquals($this->getValue($fieldXpath), $v,
                                'Stored data not equals to specified');
                        break;
                }
            } else {
                throw new OutOfRangeException("Can't find specified store view.");
            }
        }
    }

}