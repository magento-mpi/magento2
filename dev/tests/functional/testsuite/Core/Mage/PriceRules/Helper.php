<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_PriceRules
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
    protected static $_optionsNesting = 1;
    protected static $_qtyOptionsNesting = 0;
    protected static $_optionsQty = 0;

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
     * Create new Rule
     *
     * @param string|array $ruleData
     * @param string $pageToVerify
     */
    public function createRuleAndContinueEdit($ruleData, $pageToVerify = '')
    {
        $this->clickButton('add_new_rule');
        $this->fillTabs($ruleData);
        if (isset($ruleData['info']['rule_name'])) {
            $this->addParameter('elementTitle', $ruleData['info']['rule_name']);
        }
        $this->saveAndContinueEdit('button', 'save_and_continue_edit');
        if ($pageToVerify !== '') {
            $this->assertTrue($this->checkCurrentPage($pageToVerify), $this->getParsedMessages());
        }
    }

    /**
     * Filling tabs
     *
     * @param string|array $ruleData
     */
    public function fillTabs($ruleData)
    {
        $ruleData = $this->fixtureDataToArray($ruleData);
        if (isset($ruleData['info'])) {
            $this->openTab('rule_information');
            if (array_key_exists('websites', $ruleData['info'])
                && !$this->controlIsVisible('multiselect', 'websites')
            ) {
                unset($ruleData['info']['websites']);
            }
            $this->fillTab($ruleData['info'], 'rule_information');
        }
        if (isset($ruleData['conditions'])) {
            $this->fillConditionsTab($ruleData['conditions']);
        }
        if (isset($ruleData['actions'])) {
            $this->fillActionsTab($ruleData['actions']);
        }
        if (isset($ruleData['labels'])) {
            $this->fillLabelsTab($ruleData['labels']);
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
        $returnOptionsNesting = self::$_optionsNesting;
        $retQtyOptsNesting = self::$_qtyOptionsNesting;
        $returnOptionsQty = self::$_optionsQty;
        if ($fillArray) {
            $this->fillConditionFields($fillArray, $tabId, $isNested);
        }

        foreach ($conditionsData as $value) {
            if (is_array($value)) {
                $this->addConditions($value, $tabId);
            }
        }
        self::$_optionsNesting = $returnOptionsNesting;
        self::$_qtyOptionsNesting = $retQtyOptsNesting;
        self::$_optionsQty = $returnOptionsQty;
    }

    /**
     * Set conditions params
     *
     * @param string $type
     */
    public function setConditionsParams($type)
    {
        $_optionsNesting = self::$_optionsNesting;
        if (self::$_qtyOptionsNesting > 0) {
            for ($i = 1; $i < self::$_qtyOptionsNesting; $i++) {
                $_optionsNesting = self::$_optionsNesting . '--' . self::$_optionsQty;
            }
            $this->addParameter('condition', $_optionsNesting);
            self::$_optionsQty = $this->getControlCount('pageelement', 'rule_' . $type . '_item_row');
        } else {
            $this->addParameter('condition', $_optionsNesting);
            self::$_optionsQty = $this->getControlCount('pageelement', 'apply_for_rule_' . $type . '_row');
        }
        self::$_optionsNesting = $_optionsNesting;
        $this->addParameter('key', self::$_optionsQty);
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
            self::$_qtyOptionsNesting += 1;
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
            $this->_fill(array(
                'type' => $formField['type'],
                'name' => $formFieldName,
                'value' => $formField['value'],
                'locator' => $formField['path']
            ));
            $this->clearActiveFocus();
        }
    }

    /**
     * Open Rule
     *
     * @param array $searchData
     */
    public function openRule(array $searchData)
    {
        //Search Rule
        $searchData = $this->_prepareDataForSearch($searchData);
        $ruleLocator = $this->search($searchData, 'rule_search_grid');
        $this->assertNotNull($ruleLocator, 'Rule is not found with data: ' . print_r($searchData, true));
        $ruleRowElement = $this->getElement($ruleLocator);
        $ruleUrl = $ruleRowElement->attribute('title');
        //Define and add parameters for new page
        $cellId = $this->getColumnIdByName('Rule');
        $cellElement = $this->getChildElement($ruleRowElement, 'td[' . $cellId . ']');
        $this->addParameter('elementTitle', trim($cellElement->text()));
        $this->addParameter('id', $this->defineIdFromUrl($ruleUrl));
        //Open Rule
        $this->url($ruleUrl);
        $this->validatePage();
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
        $cellId = $this->getColumnIdByName('Rule');
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
        $ruleData = $this->fixtureDataToArray($ruleData);
        foreach ($ruleData as $tabName => $tabData) {
            switch ($tabName) {
                case 'info':
                    $this->openTab('rule_information');
                    if (array_key_exists('websites', $tabData) && !$this->controlIsVisible('multiselect', 'websites')) {
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
        $cellId = $this->getColumnIdByName('Rule');
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