<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\ProductDetails;

use Mtf\Client\Element;
use Mtf\Client\Driver\Selenium\Element\SuggestElement;

/**
 * Class AttributeSet
 */
class AttributeSet extends SuggestElement
{
    /**
     * Attribute Set locator
     *
     * @var string
     */
    protected $value = '.action-toggle > span';

    /**
     * Attribute Set button
     *
     * @var string
     */
    protected $actionToggle = '.action-toggle';

    /**
     * Set value
     *
     * @param string $value
     * @return void
     */
    public function setValue($value)
    {
        $this->find($this->actionToggle)->click();
        parent::setValue($value['attribute_set_name']);
    }

    /**
     * Get value
     *
     * @return null
     */
    public function getValue()
    {
        return $this->find($this->value)->getText();
    }
}
