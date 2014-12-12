<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Sales\Test\Block\Adminhtml\Order\Creditmemo;

use Magento\Sales\Test\Block\Adminhtml\Order\AbstractForm;
use Magento\Sales\Test\Block\Adminhtml\Order\Creditmemo\Form\Items;

/**
 * Class Form
 * Credit memo create form
 */
class Form extends AbstractForm
{
    /**
     * Items block css selector
     *
     * @var string
     */
    protected $items = '#creditmemo_item_container';

    /**
     * Get items block
     *
     * @return Items
     */
    protected function getItemsBlock()
    {
        return $this->blockFactory->create(
            'Magento\Sales\Test\Block\Adminhtml\Order\Creditmemo\Form\Items',
            ['element' => $this->_rootElement->find($this->items)]
        );
    }
}
