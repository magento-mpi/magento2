<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Edit\AdvancedPricingTab;

use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Options;

/**
 * Class OptionField
 * Form 'Tier prices' on the tab "Extended price"
 */
class OptionTier extends Options
{
    /**
     * 'Add Tier' button selector
     *
     * @var string
     */
    protected $buttonFormLocator = "//*[@id='tier_price_container']/following-sibling::tfoot//button";

    /**
     * Fill product form 'Tier price'
     *
     * @param array $fields
     * @param Element $element
     * @return $this
     */
    public function fillOptions(array $fields, Element $element = null)
    {
        $this->_rootElement->find($this->buttonFormLocator, Locator::SELECTOR_XPATH)->click();
        return parent::fillOptions($fields, $element);
    }
}
