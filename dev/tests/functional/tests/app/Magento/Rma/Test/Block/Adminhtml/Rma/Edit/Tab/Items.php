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

/**
 * Return Items block.
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
            $item = $this->dataMapping($item);
            $this->fillItemRow($item, $context);
        }

        $this->setFields['items'] = $fields['items']['value'];
        return $this;
    }

    /**
     * Fill data to item row.
     *
     * @param array $fields
     * @param Element $context
     * @return void
     */
    protected function fillItemRow(array $fields, Element $context)
    {
        $itemRow = $context->find(sprintf($this->rowItemByName, $fields['product']['value']), Locator::SELECTOR_XPATH);

        foreach ($fields as $field) {
            $elementType = isset($field['input']) ? $field['input'] : 'input';
            $element = $itemRow->find(
                $field['selector']. ' ' . $elementType,
                $field['strategy'],
                $field['input']
            );

            if ($element->isVisible()) {
                $element->setValue($field['value']);
            }
        }
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
            $mapping = $this->dataMapping();
            $data = [];

            $rows = $this->_rootElement->find($this->rowItem, Locator::SELECTOR_XPATH)->getElements();
            foreach ($rows as $row) {
                $data[] = $this->getItemRowData($mapping, $row);
            }

            return ['items' => $data];
        }
        return [];
    }

    /**
     * Return item row data.
     *
     * @param array $mapping
     * @param Element $row
     * @return array
     */
    protected function getItemRowData(array $mapping, Element $row)
    {
        $data = [];

        foreach ($mapping as $columnName => $locator) {
            $elementType = isset($locator['input']) ? $locator['input'] : 'input';
            $element = $row->find(
                $locator['selector']. ' ' . $elementType,
                $locator['strategy'],
                $locator['input']
            );
            $value = null;

            if ($element->isVisible()) {
                $value = $element->getValue();
            } else {
                $value = $row->find($locator['selector'], $locator['strategy'])->getText();
            }

            $data[$columnName] = trim($value);
        }

        return $data;
    }
}
