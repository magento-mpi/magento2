<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Edit;

use Mtf\Block\Form;
use Mtf\Client\Element;

/**
 * Abstract class Options
 *
 * @package Magento\Catalog\Test\Block\Adminhtml\Product\Edit\CustomOptionsTab
 */
abstract class Options extends Form
{
    /**
     * Fill the form
     *
     * @param array $fields
     * @param array $locatorPlaceholder
     * @param Element $element
     * @return $this
     */
    public function fillAnArray(array $fields, array $locatorPlaceholder = [], Element $element = null)
    {
        $mapping = $this->dataMapping($fields);
        $this->_fill($mapping, $element);
        return $this;
    }

    /**
     * Verify the form
     *
     * @param array $fields
     * @param array $locatorPlaceholder
     * @param Element $element
     * @return $this
     */
    public function verifyAnArray(array $fields, array $locatorPlaceholder = [], Element $element = null)
    {
        $mapping = $this->dataMapping($fields);
        $this->_verify($mapping, $element);
        return $this;
    }

    /**
     * Return mapping data
     *
     * @return array $mapping
     */
    public function getMapping()
    {
        return $this->mapping;
    }
} 