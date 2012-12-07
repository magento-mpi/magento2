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
     * @param $content
     */
    protected function _content($content)
    {
        if ($content) {
            $widgetsData = (isset($content['widgets'])) ? $content['widgets'] : array();
            $variableData = (isset($content['variables'])) ? $content['variables'] : array();

            foreach ($widgetsData as $widget) {
                if (!$this->cmsPagesHelper()->insertWidget($widget)) {
                    //skip next steps, because widget insertion pop-up is opened
                    return;
                }
            }
            foreach ($variableData as $variable) {
                $this->cmsPagesHelper()->insertVariable($variable);
            }
        }
    }

    /**
     * @param $blockData
     * @return array $blockData
     */
    protected function _ifIsString($blockData)
    {
        if (is_string($blockData)) {
            $elements = explode('/', $blockData);
            $fileName = (count($elements) > 1) ? array_shift($elements) : '';
            $blockData = $this->loadDataSet($fileName, implode('/', $elements));
        }
        return $blockData;
    }

    /**
     * Create a new static block.
     * Uses a simple editor only.
     *
     * @param array|string $blockData
     */
    public function createStaticBlock(array $blockData)
    {
        $blockData = $this->_ifIsString($blockData);
        $content = (isset($blockData['content'])) ? $blockData['content'] : array();
        $this->clickButton('add_new_block');
        if (array_key_exists('store_view', $blockData) && !$this->controlIsPresent('multiselect', 'store_view')) {
            unset($blockData['store_view']);
        }
        $this->fillForm($blockData);
        $this->_content($content);
        $this->saveForm('save_block');
    }

    /**
     * Opens a static block
     *
     * @param array $searchData
     */
    public function openStaticBlock(array $searchData)
    {
        if (array_key_exists('filter_store_view', $searchData)
            && !$this->controlIsPresent('dropdown', 'filter_store_view')
        ) {
            unset($searchData['filter_store_view']);
        }
        $xpathTR = $this->search($searchData, 'static_blocks_grid');
        $this->assertNotEquals(null, $xpathTR, 'Static Block is not found');
        $cellId = $this->getColumnIdByName('Title');
        $this->addParameter('tableLineXpath', $xpathTR);
        $this->addParameter('cellIndex', $cellId);
        $param = $this->getControlAttribute('pageelement', 'table_line_cell_index', 'text');
        $this->addParameter('elementTitle', $param);
        $this->addParameter('id', $this->defineIdFromTitle($xpathTR));
        $this->clickControl('pageelement', 'table_line_cell_index');
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