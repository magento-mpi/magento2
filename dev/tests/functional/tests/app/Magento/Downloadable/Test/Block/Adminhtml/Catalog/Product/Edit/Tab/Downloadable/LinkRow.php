<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Downloadable\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Downloadable;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

class LinkRow extends Block
{
    /**
     * Example: name="downloadable[link][1][price]"
     *
     * @var string
     */
    protected $fieldSelectorTemplate = '[name="downloadable[link][%d][%s]"]';

    /**
     * @param int $rowIndex
     * @param array $rowData
     */
    public function fill($rowIndex, $rowData)
    {
        foreach ([
            'title', 'price', 'number_of_downloads', 'is_unlimited',
            'is_shareable', 'sample][type', 'sample][url', 'type', 'link_url', 'sort_order'
        ] as $field) {
            if (isset($rowData[$field]['value'])) {
                $fieldSelector = sprintf($this->fieldSelectorTemplate, $rowIndex, $field);
                /* @TODO replace with typified radio element */
                $type = isset($rowData[$field]['input']) ? $rowData[$field]['input'] : null;
                if ($type == 'radio') {
                    $type = 'checkbox';
                    $fieldSelector .= sprintf('[value=%s]', $rowData[$field]['value']);
                    $rowData[$field]['value'] = 'Yes';
                }
                $this->_rootElement->find($fieldSelector, Locator::SELECTOR_CSS, $type)
                    ->setValue($rowData[$field]['value']);
            }
        }
    }
}
