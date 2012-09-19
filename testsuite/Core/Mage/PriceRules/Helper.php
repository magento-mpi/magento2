<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
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
class Core_Mage_PriceRules_Helper extends Mage_Selenium_AbstractHelper
{
    protected static $optionsNesting = 1;
    protected static $qtyOptionsNesting = 0;
    protected static $optionsQty = 0;

    /**
     * Create new Rule
     *
     * @param string|array $createRuleData
     */
    public function createRule($createRuleData)
    {
        $this->clickButton('add_new_rule');
        $this->fillTabs($createRuleData);
        $this->saveForm('save_rule');
    }

    /**
     * Filling tabs
     *
     * @param string|array $ruleData
     */
    public function fillTabs($ruleData)
    {
        if (is_string($ruleData)) {
            $elements = explode('/', $ruleData);
            $fileName = (count($elements) > 1) ? array_shift($elements) : '';
            $ruleData = $this->loadDataSet($fileName, implode('/', $elements));
        }
        $ruleInfo = (isset($ruleData['info'])) ? $ruleData['info'] : array();
        $ruleConditions = (isset($ruleData['conditions'])) ? $ruleData['conditions'] : array();
        $ruleActions = (isset($ruleData['actions'])) ? $ruleData['actions'] : array();
        $ruleLabels = (isset($ruleData['labels'])) ? $ruleData['labels'] : array();
        if (array_key_exists('websites', $ruleInfo) && !$this->controlIsPresent('multiselect', 'websites')) {
            unset($ruleInfo['websites']);
        }
        $this->fillTab($ruleInfo, 'rule_information');
        $this->fillConditionsTab($ruleConditions);
        $this->fillActionsTab($ruleActions);
        if ($ruleLabels) {
            $this->fillLabelsTab($ruleLabels);
        }
    }

    /**
     * Fill Conditions Tab
     *
     * @param array $conditionsData
     */
    public function fillConditionsTab(array $conditionsData)
    {
        $this->openTab('rule_conditions');
        $this->addConditions($conditionsData, 'rule_conditions');
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
        $this->fillTab($actionsData, 'rule_actions');
        $this->addConditions($conditionsData, 'rule_actions');
    }

    /**
     * Fill Labels Tab
     *
     * @param array $labelsData
     */
    public function fillLabelsTab(array $labelsData)
    {
        $this->openTab('rule_labels');
        $storeViewLabels = array();
        if (array_key_exists('store_view_labels', $labelsData)) {
            $storeViewLabels = $labelsData['store_view_labels'];
            unset($labelsData['store_view_labels']);
        }
        $this->fillTab($labelsData, 'rule_labels');
        foreach ($storeViewLabels as $key => $value) {
            $this->addParameter('storeViewName', $key);
            $this->fillTab(array('store_view_rule_label' => $value), 'rule_labels');
        }
    }

    /**
     * Add rule conditions
     *
     * @param array $conditionsData
     * @param string $tabId
     */
    public function addConditions(array $conditionsData, $tabId = '')
    {
        $fillArray = array();
        $isNested = false;
        foreach ($conditionsData as $key => $value) {
            if (!is_array($value)) {
                if ($key == 'select_' . preg_replace('/(^rule_)|(s$)/', '', $tabId) . '_new_child') {
                    $isNested = true;
                }
                $fillArray[$key] = $value;
                unset($conditionsData[$key]);
            }
        }
        $returnOptionsNesting = self::$optionsNesting;
        $returnQtyOptionsNesting = self::$qtyOptionsNesting;
        $returnOptionsQty = self::$optionsQty;
        if ($fillArray) {
            $this->fillConditionFields($fillArray, $tabId, $isNested);
        }

        foreach ($conditionsData as $value) {
            if (is_array($value)) {
                $this->addConditions($value, $tabId);
            }
        }
        self::$optionsNesting = $returnOptionsNesting;
        self::$qtyOptionsNesting = $returnQtyOptionsNesting;
        self::$optionsQty = $returnOptionsQty;
    }

    /**
     * Set conditions params
     *
     * @param string $type
     */
    public function setConditionsParams($type)
    {
        $optionsNesting = self::$optionsNesting;
        if (self::$qtyOptionsNesting > 0) {
            for ($i = 1; $i < self::$qtyOptionsNesting; $i++) {
                $optionsNesting = self::$optionsNesting . '--' . self::$optionsQty;
            }
            $this->addParameter('condition', $optionsNesting);
            self::$optionsQty = $this->getControlCount('pageelement', 'rule_' . $type . '_item_row');
        } else {
            $this->addParameter('condition', $optionsNesting);
            self::$optionsQty = $this->getControlCount('pageelement', 'apply_for_rule_' . $type . '_row');
        }
        self::$optionsNesting = $optionsNesting;
        $this->addParameter('key', self::$optionsQty);
    }

    /**
     * Fill data for one condition
     *
     * @param array $data
     * @param string $tabId
     * @param bool $isNested
     *
     * @throws RuntimeException
     */
    public function fillConditionFields(array $data, $tabId = '', $isNested = false)
    {
        if ($isNested) {
            self::$qtyOptionsNesting += 1;
        }
        $type = preg_replace('/(^rule_)|(s$)/', '', $tabId);
        $this->setConditionsParams($type);
        $uimapData = $this->getCurrentUimapPage()->getMainForm();
        if ($tabId && $uimapData->getTab($tabId)) {
            $uimapData = $uimapData->getTab($tabId);
        }
        $fieldsets = $uimapData->getAllFieldsets($this->getParamsHelper());
        $formDataMap = $this->_getFormDataMap($fieldsets, $data);

        foreach ($formDataMap as $formFieldName => $formField) {
            if ($formFieldName === 'category') {
                $buttonName = preg_replace('/(^rule_)|(s$)/', '', $tabId) . '_value';
                $this->clickControl('link', $buttonName, false);
                $this->clickControl('link', 'open_chooser', false);
                $this->pleaseWait();
                $categories = explode(',', $formField['value']);
                $categories = array_map('trim', $categories);
                foreach ($categories as $value) {
                    $this->categoryHelper()->selectCategory($value, 'rule_condition_item');
                }
                $this->clickControl('link', 'confirm_choice', false);
                continue;
            }
            $this->clickControl('link', preg_replace('/(^select_)|(^type_)/', '', $formFieldName), false);
            $this->_fill(array('type'  => $formField['type'], 'name' => $formFieldName,
                               'value' => $formField['value'], 'locator' => $formField['path']));
            $this->clearActiveFocus();
        }
    }

    /**
     * Open Rule
     *
     * @param array $ruleSearch
     */
    public function openRule(array $ruleSearch)
    {
        $xpathTR = $this->search($ruleSearch, 'rule_search_grid');
        $this->assertNotNull($xpathTR,
            'Rule with next search criteria:' . "\n" . implode(' and ', $ruleSearch) . "\n" . 'is not found');
        $cellId = $this->getColumnIdByName('Rule Name');
        $this->addParameter('tableLineXpath', $xpathTR);
        $this->addParameter('cellIndex', $cellId);
        $param = $this->getControlAttribute('pageelement', 'table_line_cell_index', 'text');
        $this->addParameter('elementTitle', $param);
        $this->addParameter('id', $this->defineIdFromTitle($xpathTR));
        $this->clickControl('pageelement', 'table_line_cell_index');
    }

    /**
     * Open Rule and delete
     *
     * @param array $ruleSearch
     */
    public function deleteRule(array $ruleSearch)
    {
        $this->openRule($ruleSearch);
        $this->clickButtonAndConfirm('delete_rule', 'confirmation_for_delete');
    }

    /**
     * Delete all rules
     */
    public function deleteAllRules()
    {
        $this->addParameter('tableXpath', $this->_getControlXpath('pageelement', 'rule_grid'));
        $cellId = $this->getColumnIdByName('Rule Name');
        $xpath = $this->_getControlXpath('pageelement', 'price_rule');
        $this->addParameter('tableLineXpath', $this->_getControlXpath('pageelement', 'price_rule'));
        $this->addParameter('cellIndex', $cellId);
        while (!$this->controlIsPresent('message', 'specific_table_no_records_found')) {
            $param = $this->getControlAttribute('pageelement', 'table_line_cell_index', 'text');
            $this->addParameter('elementTitle', $param);
            $this->addParameter('id', $this->defineIdFromTitle($xpath));
            $this->clickControl('pageelement', 'price_rule');
            $this->clickButtonAndConfirm('delete_rule', 'confirmation_for_delete');
        }
    }

    /**
     * Verify Rule Data
     *
     * @param array|string $ruleData
     */
    public function verifyRuleData($ruleData)
    {
        if (is_string($ruleData)) {
            $elements = explode('/', $ruleData);
            $fileName = (count($elements) > 1) ? array_shift($elements) : '';
            $ruleData = $this->loadDataSet($fileName, implode('/', $elements));
        }
        foreach ($ruleData as $tabName => $tabData) {
            switch ($tabName) {
                case 'info':
                    if (array_key_exists('websites', $tabData) && !$this->controlIsPresent('multiselect', 'websites')) {
                        unset($tabData['websites']);
                    }
                    $this->verifyForm($tabData, 'rule_information');
                    break;
                case 'conditions':
                    //@TODO verify Conditions
                    break;
                case 'actions':
                    $this->verifyForm($tabData, 'rule_actions');
                    //@TODO verify action conditions for Shopping Cart Price Rule
                    break;
                case 'labels':
                    //@TODO verify Store Labels for Shopping Cart Price Rule
                    break;
                default:
                    break;
            }
        }
        $this->assertEmptyVerificationErrors();
    }

    /**
     * Sets all created Rules as inactive (PreConditions for prices verification in frontend)
     *
     * @return bool
     */
    public function setAllRulesToInactive()
    {
        $xpathTR = $this->search(array('filter_status' => 'Active'), 'rule_search_grid');
        if (!$xpathTR) {
            return true;
        }
        $cellId = $this->getColumnIdByName('Rule Name');
        $this->addParameter('tableLineXpath', $xpathTR);
        $this->addParameter('cellIndex', $cellId);
        while ($this->controlIsPresent('pageelement', 'table_line_cell_index')) {
            $param = $this->getControlAttribute('pageelement', 'table_line_cell_index', 'text');
            $this->addParameter('elementTitle', $param);
            $this->addParameter('id', $this->defineIdFromTitle($xpathTR));
            $this->clickControl('pageelement', 'table_line_cell_index');
            $this->fillTab(array('status' => 'Inactive'), 'rule_information');
            $this->saveForm('save_rule');
        }
        return true;
    }

    /**
     * Edit Created Rule
     *
     * @param array $editRuleData
     * @param array $ruleSearchCreated
     */
    public function editRule($editRuleData, $ruleSearchCreated)
    {
        $this->openRule($ruleSearchCreated);
        $this->fillTabs($editRuleData);
        $this->saveForm('save_rule');
    }
}