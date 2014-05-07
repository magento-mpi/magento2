<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Edit;

use Mtf\Client\Element;
use Magento\Backend\Test\Block\Widget\Tab;

/**
 * Abstract class Options
 * Parent class for all forms of product options
 *
 * @package Magento\Catalog\Test\Block\Adminhtml\Product\Edit
 */
abstract class Options extends Tab
{
    /**
     * Fills in the form of an array of input data
     *
     * @param array $fields
     * @param Element $element
     * @return $this
     */
    public function fillOptions(array $fields, Element $element = null)
    {
        $element = $element === null ? $this->_rootElement : $element;
        $mapping = $this->dataMapping($fields);
        $this->_fill($mapping, $element);
        return $this;
    }
}
