<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle;

use Mtf\Block\Block;
use Mtf\Client\Element;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;

/**
 * Class Option
 * Bundle options
 */
class Option extends Block
{
    /**
     * Grid to assign products to bundle option
     *
     * @var string
     */
    protected $searchGridBlock = '[role=dialog][style*="display: block;"]';

    /**
     * Added product row
     *
     * @var string
     */
    protected $selectionBlock = '#bundle_selection_row';

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
     * Bundle option type
     *
     * @var string
     */
    protected $type = '[name$="[type]"]';

    /**
     * Determine whether bundle options is require to fill
     *
     * @var string
     */
    protected $required = '#field-option-req';

    /**
     * Get grid for assigning products for bundle option
     *
     * @param Element $context
     * @return \Magento\Bundle\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle\Option\Search\Grid
     */
    protected function getSearchGridBlock(Element $context = null)
    {
        $element = $context ? $context : $this->_rootElement;
        return Factory::getBlockFactory()->getMagentoBundleAdminhtmlCatalogProductEditTabBundleOptionSearchGrid(
            $element->find($this->searchGridBlock, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get product row assigned to bundle option
     *
     * @param int $rowNumber
     * @param Element $context
     * @return \Magento\Bundle\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle\Option\Selection
     */
    protected function getSelectionBlock($rowNumber, Element $context = null)
    {
        $element = $context ? $context : $this->_rootElement;
        return Factory::getBlockFactory()->getMagentoBundleAdminhtmlCatalogProductEditTabBundleOptionSelection(
            $element->find($this->selectionBlock . '_' . $rowNumber)
        );
    }

    /**
     * Expand block
     */
    public function expand()
    {
        if (!$this->_rootElement->find($this->title, Locator::SELECTOR_CSS)->isVisible()) {
            $this->_rootElement->find($this->optionToggle, Locator::SELECTOR_CSS)->click();
        }
    }

    /**
     * Fill bundle option
     *
     * @param array $fields
     * @param Element $context
     */
    public function fillBundleOption(array $fields, Element $context)
    {
        $rowNumber = 0;
        $this->fillOptionData($fields);
        foreach ($fields['assigned_products'] as $field) {
            if (is_array($field)) {
                $this->_rootElement->find($this->addProducts, Locator::SELECTOR_CSS)->click();
                $searchBlock = $this->getSearchGridBlock($context);
                $searchBlock->searchAndSelect($field['search_data']);
                $searchBlock->addProducts();
                $this->getSelectionBlock($rowNumber)->fillProductRow($field['data']);
                $rowNumber++;
            }
        }
    }

    /**
     * Update bundle option (now only general data, skipping assignments)
     *
     * @param array $fields
     */
    public function updateBundleOption(array $fields)
    {
        $this->fillOptionData($fields);
    }

    /**
     * Fill in general data to bundle option
     *
     * @param array $fields
     */
    private function fillOptionData(array $fields)
    {
        $this->_rootElement->find($this->title)->setValue($fields['title']['value']);
        $this->_rootElement->find($this->type, Locator::SELECTOR_CSS, 'select')->setValue($fields['type']['value']);
        $this->_rootElement->find($this->required, Locator::SELECTOR_CSS, 'checkbox')
            ->setValue($fields['required']['value']);
    }
}
