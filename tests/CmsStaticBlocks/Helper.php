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
class CmsStaticBlocks_Helper extends Mage_Selenium_TestCase
{

    /**
     * Create a new static block.
     * Uses a simple editor only.
     *
     * @param array $blockData
     */
    public function createStaticBlock(array $blockData)
    {
        $blockData = $this->arrayEmptyClear($blockData);

        $this->clickButton('add_new_block');
        // Check if store views are present
        if (!$this->controlIsPresent('multiselect', 'store_views') && isset($blockData['store_views'])) {
            unset($blockData['store_views']);
        }
        //
        $widget = (isset($blockData['widget_data'])) ? $blockData['widget_data'] : null;
        $image = (isset($blockData['image_data'])) ? $blockData['image_data'] : null;
        $variable = (isset($blockData['variable_data'])) ? $blockData['variable_data'] : null;
        // Switch to simple editor
        if (!$this->controlIsPresent('field', 'simple_editor_content') && $this->buttonIsPresent('show_hide_editor')) {
            $this->clickButton('show_hide_editor', false);
        }
        //
        $this->fillForm($blockData);
        if ($widget) {
            $this->addWidget($widget);
        }
        if ($image) {
            $this->addImage($image);
        }
        if ($variable) {
            $this->addVariable($variable);
        }
        //
        $this->saveForm('save_block');
    }

    /**
     * Adds a widget to the static block
     *
     * @param type $widgetDataSet
     */
    public function addWidget($widgetDataSet)
    {
        $this->arrayEmptyClear($widgetDataSet);
        if (!$this->buttonIsPresent('editor_insert_widget') && $this->buttonIsPresent('show_hide_editor')) {
            $this->clickButton('show_hide_editor', false);
        }
        foreach ($widgetDataSet as $widgetData) {
            if (!$this->buttonIsPresent('editor_insert_variable') && $this->buttonIsPresent('show_hide_editor')) {
                $this->clickButton('show_hide_editor', false);
            }
            $this->clickButton('editor_insert_widget', false);
            $xpathInsertWidgetButton = $this->_getControlXpath('button', 'insert_widget');
            $this->waitForElement($xpathInsertWidgetButton);
        //@TODO
        }
    }

    /**
     * Adds an image to the static block
     *
     * @param type $imageData
     */
    public function addImage($imageData)
    {
        if (!$this->buttonIsPresent('editor_insert_image') && $this->buttonIsPresent('show_hide_editor')) {
            $this->clickButton('show_hide_editor', false);
        }
        //@TODO. Flash buttons cannot be supported now.
    }

    /**
     * Adds a variable to the static block
     *
     * @param array $variableSet Array of variable names
     */
    public function addVariable(array $variableSet)
    {
        foreach ($variableSet as $variableName) {
            $this->addParameter('variableName', $variableName);
            if (!$this->buttonIsPresent('editor_insert_variable') && $this->buttonIsPresent('show_hide_editor')) {
                $this->clickButton('show_hide_editor', false);
            }
            $this->clickButton('editor_insert_variable', false);
            $xpathVariable = $this->_getControlXpath('link', 'variable');
            $this->waitForElement($xpathVariable);
            $this->clickControl('link', 'variable', false);
        }
    }

    /**
     * Opens a static block
     *
     * @param array $searchData
     */
    public function openStaticBlock(array $searchData)
    {
        $searchData = $this->arrayEmptyClear($searchData);
        // Check if store views are available
        $key = 'filter_store_view';
        if (array_key_exists($key, $searchData)) {
            if (!$this->controlIsPresent('dropdown', 'store_view')) {
                unset($searchData[$key]);
            }
        }
        // Search and open
        $this->navigate('manage_cms_static_blocks');
        $xpathTR = $this->search($searchData, 'static_blocks_grid');
        $this->assertNotEquals(null, $xpathTR, 'Static Block ' . implode(',', $searchData) . ' is not found');
        $names = $this->shoppingCartHelper()->getColumnNamesAndNumbers('static_block_grid_head', false);
        if (array_key_exists('Title', $names)) {
            $text = $this->getText($xpathTR . '//td[' . $names['Title'] . ']');
            $this->addParameter('blockName', $text);
        }
        $this->addParameter('id', $this->defineIdFromTitle($xpathTR));
        $this->click($xpathTR . '//td[' . $names['Title'] . ']');
        $this->waitForPageToLoad($this->_browserTimeoutPeriod);
        $this->validatePage();
    }

    /**
     * Deletes a static block
     *
     * @param array $searchData
     */
    public function deleteStaticBlock(array $searchData)
    {
        $this->openStaticBlock($searchData);
        $this->clickButtonAndConfirm('delete_block', 'confirmation_for_delete');
    }

}
