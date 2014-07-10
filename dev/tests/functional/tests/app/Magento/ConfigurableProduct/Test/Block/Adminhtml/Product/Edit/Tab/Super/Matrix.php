<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\Block\Adminhtml\Product\Edit\Tab\Super;

use Mtf\Client\Element;
use Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Options as CatalogOptions;

/**
 * Class Matrix
 * Matrix row form
 */
class Matrix extends CatalogOptions
{
    /**
     * CSS selector data cell
     *
     * @var string
     */
    protected $cellSelector = 'td:nth-child(%d)';

    /**
     * Field name mapping
     *
     * @var array
     */
    protected $fieldNameMapping = [
        3 => 'name',
        4 => 'sku',
        5 => 'price',
        6 => 'qty',
        7 => 'weight'
    ];

    /**
     * Getting product matrix data form on the product form
     *
     * @param array $fields
     * @param Element $element
     * @return $this
     */
    public function getDataOptions(array $fields = null, Element $element = null)
    {
        $element = $element === null ? $this->_rootElement : $element;
        $mapping = $this->dataMapping($fields);
        $data = $this->_getData($mapping, $element);

        $column = 3;
        $cell = $element->find(sprintf($this->cellSelector, $column));
        $data['options_names'] = [];
        while ($cell->isVisible()) {
            if (isset($this->fieldNameMapping[$column])) {
                $data[$this->fieldNameMapping[$column]] = $cell->getText();
            } else {
                $data['options_names'][] = $cell->getText();
            }
            $cell = $element->find(sprintf($this->cellSelector, ++$column));
        }

        return $data;
    }
}
