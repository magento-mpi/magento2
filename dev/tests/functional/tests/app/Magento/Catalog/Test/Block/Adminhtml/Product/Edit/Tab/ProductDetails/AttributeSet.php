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
 * Set and Get Attribute Set on the Product form
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
        if ($value !== 'Default') {
            parent::setValue($value);
        }
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->find($this->value)->getText();
    }
}
