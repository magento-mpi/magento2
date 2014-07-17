<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle;

use Mtf\Block\Form;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Magento\Bundle\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle\Option\Selection;
use Magento\Bundle\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle\Option\Search\Grid;

/**
 * Class Option
 * Bundle option block on backend
 */
class Option extends Form
{
    /**
     * Selector block Grid
     *
     * @var string
     */
    protected $searchGridBlock = "ancestor::body//div[contains(@style,'display: block') and @role='dialog']";

    /**
     * Added product row
     *
     * @var string
     */
    protected $selectionBlock = './/tr[contains(@id, "bundle_selection_row_")][not(@style="display: none;")][%d]';

    /**
     * 'Add Products to Option' button
     *
     * @var string
     */
    protected $addProducts = '[data-ui-id$=add-selection-button]';

    /**
     * Bundle option toggle
     *
     * @var string
     */
    protected $optionToggle = '[data-target$=content]';

    /**
     * Bundle option title
     *
     * @var string
     */
    protected $title = '[name$="[title]"]';

    /**
     * Remove selection button selector
     *
     * @var string
     */
    protected $removeSelection = '[class = "action- scalable delete icon-btn"]';

    /**
     * Get grid for assigning products for bundle option
     *
     * @return Grid
     */
    protected function getSearchGridBlock()
    {
        return $this->blockFactory->create(
            'Magento\Bundle\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle\Option\Search\Grid',
            ['element' => $this->_rootElement->find($this->searchGridBlock, Locator::SELECTOR_XPATH)]
        );
    }

    /**
     * Get product row assigned to bundle option
     *
     * @param int $rowIndex
     * @return Selection
     */
    protected function getSelectionBlock($rowIndex)
    {
        return $this->blockFactory->create(
            'Magento\Bundle\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle\Option\Selection',
            ['element' => $this->_rootElement->find(sprintf($this->selectionBlock, $rowIndex), Locator::SELECTOR_XPATH)]
        );
    }

    /**
     * Expand block
     *
     * @return void
     */
    public function expand()
    {
        if (!$this->_rootElement->find($this->title)->isVisible()) {
            $this->_rootElement->find($this->optionToggle)->click();
        }
    }

    /**
     * Fill bundle option
     *
     * @param array $fields
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function fillBundleOption(array $fields)
    {
        $mapping = $this->dataMapping($fields);
        $this->_fill($mapping);
        $selections = $this->_rootElement->find($this->removeSelection)->getElements();
        if (count($selections)) {
            foreach ($selections as $itemSelection) {
                $itemSelection->click();
            }
        }
        foreach ($fields['assigned_products'] as $key => $field) {
            $this->_rootElement->find($this->addProducts)->click();
            $searchBlock = $this->getSearchGridBlock();
            $searchBlock->searchAndSelect($field['search_data']);
            $searchBlock->addProducts();
            $this->getSelectionBlock(++$key)->fillProductRow($field['data']);
        }
    }

    /**
     * Get data bundle option
     *
     * @param array $fields
     * @return array
     */
    public function getBundleOptionData(array $fields)
    {
        $mapping = $this->dataMapping($fields);
        $newField = $this->_getData($mapping);
        foreach ($fields['assigned_products'] as $key => $field) {
            $newField['assigned_products'][$key] = $this->getSelectionBlock($key + 1)->getProductRow($field['data']);
        }
        return $newField;
    }

    /**
     * Update bundle option (now only general data, skipping assignments)
     *
     * @param array $fields
     * @return void
     */
    public function updateBundleOption(array $fields)
    {
        $this->fillBundleOption($fields);
    }
}
