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
use Mtf\Block\Block;

/**
 * Select Type
 */
class SpecialOption extends Block
{
    /**
     * Fill
     *
     * @param array $data
     */
    public function fill($data)
    {
        if (isset($data['value'])) {
            $this->_rootElement->find('#special_price', Locator::SELECTOR_CSS)->setValue($data['value']);
        }
    }
}
