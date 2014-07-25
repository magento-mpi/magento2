<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Edit\AdvancedPricingTab;

use Mtf\Client\Element;
use Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\Options\AbstractOptions;

/**
 * Class OptionTier
 * Form 'Tier prices' on the 'Advanced Pricing' tab
 */
class OptionTier extends AbstractOptions
{
    /**
     * 'Add Tier' button selector
     *
     * @var string
     */
    protected $buttonFormLocator = "#tiers_table tfoot button";

    /**
     * Fill product form 'Tier price'
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
