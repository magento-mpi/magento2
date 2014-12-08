<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Edit\AdvancedPricingTab;

use Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\Options\AbstractOptions;
use Mtf\Client\Element;

/**
 * Class OptionField
 * Form "Group prices" on the tab "Extended price"
 */
class OptionGroup extends AbstractOptions
{
    /**
     * 'Add Group Price' button selector
     *
     * @var string
     */
    protected $buttonFormLocator = "#group_prices_table tfoot button";

    /**
     * Fill the form 'Extended price'
     *
     * @param array $fields
     * @param Element $element
     * @return $this
     */
    public function fillOptions(array $fields, Element $element = null)
    {
        $this->_rootElement->find($this->buttonFormLocator)->click();
        return parent::fillOptions($fields, $element);
    }
}
