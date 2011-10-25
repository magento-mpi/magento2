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
     * List of fields that are used for searching.
     *
     * @var array
     */
    protected $_searchFields = array('block_title', 'block_identifier', 'store_views');

    /**
     * Checks if the WYSIWYG Editor is open now. Can be configured in System->Configuration.
     */
    protected function _isWysiwygEditorOpen()
    {
        return $this->controlIsPresent('link', 'wysiwyg_insert_widget');
    }

    /**
     * Checks if the WYSIWYG Editor can be opened. Can be configured in System->Configuration.
     */
    protected function _isWysiwygEditorAvailable()
    {
        return $this->buttonIsPresent('show_hide_editor');
    }

    /**
     * Open WYSIWYG Editor.
     */
    protected function _openWysiwygEditor()
    {
        if (!($this->_isWysiwygEditorAvailable()))
            $this->fail("The WYSIWYG Editor cannot be opened due to System configuration.");
        if (!($this->_isWysiwygEditorOpen()))
            $this->clickButton('show_hide_editor', false);
        $this->assertTrue($this->_isWysiwygEditorAvailable());
    }

    /**
     * Open Text Editor.
     */
    protected function _openSimpleEditor()
    {
        if ($this->_isWysiwygEditorOpen())
            $this->clickButton('show_hide_editor', false);
        $this->assertFalse($this->_isWysiwygEditorOpen());
    }

    /**
     * Add a variable to the Static Block
     *
     * @param string $varName Name of the variable to insert.
     */
    public function insertVariable($varName)
    {
        if ($this->_isWysiwygEditorOpen()) {
            $this->clickControl('link', 'wysiwyg_insert_variable', false);
        } else {
            $this->clickButton('editor_insert_variable');
        }
        $this->addParameter('variableName', $varName);
        $this->clickControl('link', 'variable', false);
    }

//    /**
//     * Add a widget to the Static Block
//     *
//     * @param string|array $widgetOptions
//     */
//    public function insertWidget($widgetOptions)
//    {
//        if ($this->_isWysiwygEditorOpen()) {
//            $this->clickControl('link', 'wysiwyg_insert_widget', false);
//        } else {
//            $this->clickButton('editor_insert_widget');
//        }
//        //TODO: implement insertWidget
//    }

    /**
     * Fill settings for a Static Block
     *
     * @param string|array $settings Refers to/Contains DataSet for filling of the current form
     */
    public function fillSettings($settings)
    {
        if (is_string($settings))
            $settings = $this->loadData($settings);
        $settings = $this->arrayEmptyClear($settings);
        $this->_openSimpleEditor();
        // Check if Store Views is present on the page
        if (!($this->isElementPresent($this->_getControlXpath('multiselect', 'store_views'))))
            unset($settings['store_views']);
        $this->fillForm($settings, 'general_information');
    }

    /**
     * Create new Static Block
     *
     * @param array $attrSet Array which contains DataSet for filling of the current form
     */
    public function createStaticBlock(array $attrSet)
    {
        $this->clickButton('add_new_block');
        $this->fillSettings($attrSet);
        $this->saveForm('save_block');
    }

    /**
     * Open Static Block
     *
     * @param string|array $block Block to open. Either a dataset name (string) or the whole data set (array).
     * @param array $dates Dates created/modified. Additional search fields.
     *                     Note, that dates should be specified as in the grid, e.g. "Oct 19".
     */
    public function openStaticBlock($block = null, array $dates = null)
    {
        $block = isset($block) ? $block : array();
        $dates = isset($dates) ? $dates : array();
        // Load data if needed.
        if (is_string($block))
            $block = $this->loadData($block);
        // Remove fields not used in search grid.
        foreach ($block as $key => $value) {
            if (!(in_array($key, $this->_searchFields)))
                $block[$key] = '%noValue%';
        }
        //Append additional search fields.
        if (isset($dates))
            $searchBlock = array_merge($block, $dates);
        if (empty($searchBlock))
            $this->fail('Nothing to open');

        // Open the search page.
        $this->navigate('manage_cms_static_blocks');
        // Check if store views are dislayed
        if (!($this->controlIsPresent('dropdown', 'store_view')))
            $searchBlock['store_views'] = '%noValue%';
        // Search for the element.
        $this->assertTrue($this->searchAndOpen($searchBlock), "Block with name '$searchBlock[block_title]' not found");
    }

    /**
     * Delete a Static Block.
     * The static block needs to be opened first.
     */
    public function deleteStaticBlock()
    {
        $this->clickButtonAndConfirm('delete_block', 'confirmation_for_delete');
    }

}
