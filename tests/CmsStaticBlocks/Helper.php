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
        $content = (isset($blockData['content'])) ? $blockData['content'] : NULL;
        $this->clickButton('add_new_block');
        if ($blockData) {
            $this->fillGeneralInfo($blockData);
        }
        if ($content) {
            $this->fillContent($content);
        }
        $this->saveForm('save_block');
    }

    /**
     * Fills General Information
     *
     * @param array $generalInfo
     */
    public function fillGeneralInfo(array $generalInfo)
    {
        if ($this->controlIsPresent('multiselect', 'store_view')) {
            if (!array_key_exists('store_view', $generalInfo)) {
                $generalInfo['store_view'] = 'All Store Views';
            }
        } elseif (!$this->controlIsPresent('multiselect', 'store_view')) {
            if (array_key_exists('store_view', $generalInfo)) {
                unset($generalInfo['store_view']);
            }
        }
        $this->fillForm($generalInfo);
    }

    /**
     * Fills Content tab
     *
     * @param string|array $content
     */
    public function fillContent($content)
    {
        if (is_string($content)) {
            $content = $this->loadData($content);
        }
        $content = $this->arrayEmptyClear($content);
        $this->fillForm($content);
        if (array_key_exists('widgets', $content)) {
            $this->addWidgets($content['widgets']);
        }
        if (array_key_exists('variables', $content)) {
            $this->addVariables($content['variables']);
        }
    }

    /**
     * Adds a widget
     *
     * @param type $widgets
     */
    public function addWidgets($widgets)
    {
        if (!$this->buttonIsPresent('editor_insert_variable') && $this->buttonIsPresent('show_hide_editor')) {
            $this->clickButton('show_hide_editor', false);
        }
        foreach ($widgets as $key => $value) {
            $options = (isset($value['chosen_option'])) ? $value['chosen_option'] : null;
            $this->clickButton('editor_insert_widget', FALSE);
            $this->waitForAjax();
            $this->fillForm($value);
            if ($options) {
                $this->cmsWidgetsHelper()->fillSelectOption($options);
            }
            $this->clickButton('submit_widget_insert', FALSE);
            // Check there are no validation errors on the page
            $this->assertFalse($this->validationMessage('widget_validation_message'), $this->messages);
            $this->waitForAjax();
        }
    }

//    /**
//     * Adds an image
//     *
//     * @param type $images
//     */
//    public function addImages($images)
//    {
//        if (!$this->buttonIsPresent('editor_insert_image') && $this->buttonIsPresent('show_hide_editor')) {
//            $this->clickButton('show_hide_editor', false);
//        }
//        //@TODO. Flash buttons cannot be supported now.
//    }

    /**
     * Adds a variable
     *
     * @param array $variables Array of variable names
     */
    public function addVariables(array $variables)
    {
        foreach ($variables as $variableName) {
            $this->addParameter('variableName', $variableName);
            if (!$this->buttonIsPresent('editor_insert_variable') && $this->buttonIsPresent('show_hide_editor')) {
                $this->clickButton('show_hide_editor', false);
            }
            $this->clickButton('editor_insert_variable', false);
            $this->waitForAjax();
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
