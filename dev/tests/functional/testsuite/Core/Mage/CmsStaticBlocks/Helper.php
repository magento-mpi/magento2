<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_CmsStaticBlocks
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
class Core_Mage_CmsStaticBlocks_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * Create a new static block.
     * Uses a simple editor only.
     *
     * @param array|string $blockData
     */
    public function createStaticBlock($blockData)
    {
        $blockData = $this->fixtureDataToArray($blockData);
        $this->clickButton('add_new_block');
        if (array_key_exists('store_view', $blockData) && !$this->controlIsVisible('multiselect', 'store_view')) {
            unset($blockData['store_view']);
        }
        if (isset($blockData['content'])) {
            $widgetsData = (isset($blockData['content']['widgets'])) ? $blockData['content']['widgets'] : array();
            $variableData = (isset($blockData['content']['variables'])) ? $blockData['content']['variables'] : array();
            foreach ($widgetsData as $widget) {
                if (!$this->cmsPagesHelper()->insertWidget($widget)) {
                    //skip next steps, because widget insertion pop-up is opened
                    return;
                }
            }
            foreach ($variableData as $variable) {
                $this->cmsPagesHelper()->insertVariable($variable);
            }
            unset($blockData['content']);
        }
        $this->fillFieldset($blockData, 'general_information');
        $this->saveForm('save_block');
    }

    /**
     * Opens a static block
     *
     * @param array $searchData
     */
    public function openStaticBlock(array $searchData)
    {
        if (isset($searchData['filter_store_view']) && !$this->controlIsVisible('dropdown', 'filter_store_view')) {
            unset($searchData['filter_store_view']);
        }
        //Search Static Block
        $searchData = $this->_prepareDataForSearch($searchData);
        $blockLocator = $this->search($searchData, 'static_blocks_grid');
        $this->assertNotNull($blockLocator, 'Static Block is not found with data: ' . print_r($searchData, true));
        $blockRowElement = $this->getElement($blockLocator);
        $blockUrl = $blockRowElement->attribute('title');
        //Define and add parameters for new page
        $cellId = $this->getColumnIdByName('Title');
        $cellElement = $this->getChildElement($blockRowElement, 'td[' . $cellId . ']');
        $this->addParameter('elementTitle', trim($cellElement->text()));
        $this->addParameter('id', $this->defineIdFromUrl($blockUrl));
        //Open Static Block
        $this->url($blockUrl);
        $this->validatePage('edit_cms_static_block');
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
