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
class CmsStaticBlocks_Helper extends Mage_Selenium_TestCase {

    /**
     * Create new Static Block
     *
     * @param array $attrSet Array which contains DataSet for filling of the current form
     */
    public function createStaticBlock(array $attrSet) {
        $attrSet = $this->arrayEmptyClear($attrSet);
        $this->clickButton('add_new_block');
        //Switch to simple text editor
        $this->clickButton('show_hide_editor', false);
        $this->fillForm($attrSet, 'general_information');
        $this->saveForm('save_block');
    }

    /**
     * Open Static Block
     *
     * @param string|array $block Block to open. Either a dataset name (string) or the whole data set (array).
     */
    public function openStaticBlock($block) {
        // Load data if needed.
        if (is_string($block))
            $block = $this->loadData($block);
        // Remove the values that should not be used for searching.
        $blockDataPattern = $this->loadData('search_block');
        foreach ($blockDataPattern as $key => $value) {
            if ($value == '%noValue%')
                $block[$key] = '%noValue%';
        }
        // Open the search page.
        $this->navigate('manage_static_blocks');
        // Search for the element.
        $this->assertTrue($this->searchAndOpen($block), "Static Block with name '$block[block_title]' is not found");
    }

    /**
     * Delete a Static Block
     *
     */
    public function deleteStaticBlock() {
        $this->clickButton('delete_block');
    }

}
