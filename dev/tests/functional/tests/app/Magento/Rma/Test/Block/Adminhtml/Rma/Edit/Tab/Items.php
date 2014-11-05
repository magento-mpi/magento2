<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Test\Block\Adminhtml\Rma\Edit\Tab;

use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Magento\Rma\Test\Block\Adminhtml\Rma\Edit\Tab\Items\Item;

/**
 * Items block on edit rma backend page.
 */
class Items extends \Magento\Backend\Test\Block\Widget\Tab
{
    /**
     * Locator for item row in grid.
     *
     * @var string
     */
    protected $rowItem = './/*[@id="magento_rma_item_edit_grid_table"]/tbody/tr';

    /**
     * Locator for search item row by name.
     *
     * @var string
     */
    protected $rowItemByName = "//tr[contains(normalize-space(td/text()),'%s')]";

    /**
     * Fill data to fields on tab.
     *
     * @param array $fields
     * @param Element|null $element
     * @return $this
     */
    public function fillFormTab(array $fields, Element $element = null)
    {
        $items = isset($fields['items']['value']) ? $fields['items']['value'] : [];
        $context = $element ? $element : $this->_rootElement;

        foreach ($items as $item) {
            $itemElement = $context->find(sprintf($this->rowItemByName, $item['product']));
            $this->getItemRow($itemElement)->fillRow($item);
        }

        $this->setFields['items'] = $fields['items']['value'];
        return $this;
    }

    /**
     * Get data of tab.
     *
     * @param array|null $fields
     * @param Element|null $element
     * @return array
     */
    public function getDataFormTab($fields = null, Element $element = null)
    {
        if (null === $fields || isset($fields['items'])) {
            $rows = $this->_rootElement->find($this->rowItem, Locator::SELECTOR_XPATH)->getElements();
            $data = [];

            foreach ($rows as $row) {
                $data[] = $this->getItemRow($row)->getRowData();
            }

            return ['items' => $data];
        }
        return [];
    }

    /**
     * Return item row form.
     *
     * @param Element $element
     * @return Item
     */
    protected function getItemRow(Element $element)
    {
        return $this->blockFactory->create(
            'Magento\Rma\Test\Block\Adminhtml\Rma\Edit\Tab\Items\Item',
            ['element' => $element]
        );
    }
}
