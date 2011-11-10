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
class PriceRules_Helper extends Mage_Selenium_TestCase
{

    /**
     * Create new Rule
     *
     * @param string|array $ruleData
     */
    public function createRule($ruleData)
    {
        if (is_string($ruleData)) {
            $ruleData = $this->loadData($ruleData);
        }
        $ruleData = $this->arrayEmptyClear($ruleData);
        $ruleInfo = (isset($ruleData['info'])) ? $ruleData['info'] : array();
        $ruleConditions = (isset($ruleData['conditions'])) ? $ruleData['conditions'] : array();
        $ruleActions = (isset($ruleData['actions'])) ? $ruleData['actions'] : array();
        $ruleLabels = (isset($ruleData['labels'])) ? $ruleData['labels'] : null;

        $this->clickButton('add_new_rule');
        if (array_key_exists('websites', $ruleInfo) && !$this->controlIsPresent('multiselect', 'websites')) {
            unset($ruleInfo['websites']);
        }
        $this->fillSimpleTab($ruleInfo, 'rule_information');
        $this->fillConditionsTab($ruleConditions);
        $this->fillActionsTab($ruleActions);
        if ($ruleLabels) {
            $this->fillLabelsTab($ruleLabels);
        }
        $this->saveForm('save_rule');
    }

    /**
     * Fill Conditions Tab
     *
     * @param array $conditionsData
     */
    public function fillConditionsTab(array $conditionsData)
    {
        $this->openTab('rule_conditions');
        $this->addConditions($conditionsData, 'apply_for_rule_conditions');
    }

    /**
     * Fill Actions Tab
     *
     * @param array $actionsData
     */
    public function fillActionsTab(array $actionsData)
    {
        $conditionsData = array();
        if (array_key_exists('action_conditions', $actionsData)) {
            $conditionsData = $actionsData['action_conditions'];
            unset($actionsData['action_conditions']);
        }
        $this->fillSimpleTab($actionsData, 'rule_actions');
        $this->addConditions($conditionsData, 'apply_for_cart_items_rule_conditions');
    }

    /**
     * Fill Labels Tab
     *
     * @param array $conditionsData
     */
    public function fillLabelsTab(array $labelsData)
    {
        $this->openTab('rule_labels');
        $storViewlabels = array();
        if (array_key_exists('store_view_labels', $labelsData)) {
            $storViewlabels = $labelsData['store_view_labels'];
            unset($labelsData['store_view_labels']);
        }
        $this->fillForm($labelsData, 'labels');
        foreach ($storViewlabels as $key => $value) {
            $this->addParameter('storeViewName', $key);
            $this->fillForm(array('store_view_rule_label' => $value), 'labels');
        }
    }

    /**
     * Open Tab
     *
     * @param string $tabName
     */
    public function openTab($tabName)
    {
        $tabXpath = $this->_getControlXpath('tab', $tabName);
        $isTabOpened = $this->getAttribute($tabXpath . '/parent::*/@class');
        if (!preg_match('/active/', $isTabOpened)) {
            $this->clickControl('tab', $tabName, false);
        }
    }

    /**
     * Add Conditions
     *
     * @param array $conditionsData
     */
    public function addConditions(array $conditionsData, $fieldSet = '')
    {
        
    }

    /**
     * Open Rule
     *
     * @param array $productSearch
     */
    public function openRule(array $ruleSearch)
    {
        $ruleSearch = $this->arrayEmptyClear($ruleSearch);
        $xpathTR = $this->search($ruleSearch, 'rule_search_grid');
        $this->assertNotEquals(null, $xpathTR, 'Rule is not found');
        $names = $this->shoppingCartHelper()->getColumnNamesAndNumbers('grid_head', false);
        if (array_key_exists('Rule Name', $names)) {
            $text = trim($this->getText($xpathTR . '//td[' . $names['Rule Name'] . ']'));
            $this->addParameter('elementTitle', $text);
        }
        $this->addParameter('id', $this->defineIdFromTitle($xpathTR));
        $this->click($xpathTR);
        $this->waitForPageToLoad($this->_browserTimeoutPeriod);
        $this->validatePage($this->_findCurrentPageFromUrl($this->getLocation()));
    }

    /**
     * Open Rule and delete
     *
     * @param array $productSearch
     */
    public function deleteRule(array $ruleSearch = array())
    {
        if ($ruleSearch) {
            $this->openRule($ruleSearch);
        }
        $this->clickButtonAndConfirm('delete_rule', 'confirmation_for_delete');
    }

    /**
     * Verify Rule Data
     *
     * @param array|string $ruleData
     */
    public function verifyRuleData($ruleData)
    {
        if (is_string($ruleData)) {
            $ruleData = $this->loadData($ruleData);
        }
        $ruleData = $this->arrayEmptyClear($ruleData);
        $simpleVerify = array();
        $specialVerify = array();
        foreach ($ruleData as $tabName => $tabData) {
            if (is_array($tabData)) {
                foreach ($tabData as $fieldKey => $fieldValue) {
                    if (is_array($fieldValue)) {
                        $specialVerify[$fieldKey] = $fieldValue;
                    } else {
                        $simpleVerify[$fieldKey] = $fieldValue;
                    }
                }
            }
        }
        $this->assertTrue($this->verifyForm($simpleVerify), $this->messages);
        //@TODO verify Conditions and storeView titles
    }

}
