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
class GroupOption extends Block
{
    /**
     * Fill
     *
     * @param string $rowPrefix
     * @param array $data
     */
    public function fill($rowPrefix, $data)
    {
        if (isset($data['website'])) {
            $this->_rootElement
                ->find('#' . $rowPrefix . '_website', Locator::SELECTOR_CSS, 'select')
                ->setValue($data['website']);
        }
        if (isset($data['customer_group'])) {
            $this->_rootElement
                ->find('#' . $rowPrefix . '_cust_group', Locator::SELECTOR_CSS, 'select')
                ->setValue($data['customer_group']);
        }
        if (isset($data['quantity'])) {
            $this->_rootElement->find('#' . $rowPrefix . '_qty')->setValue($data['price']);
        }
        if (isset($data['price'])) {
            $this->_rootElement->find('#' . $rowPrefix . '_price')->setValue($data['price']);
        }
    }
}
