<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\GiftRegistry\Test\Block\Adminhtml\Customer\Edit\Tab;

use Magento\Backend\Test\Block\Widget\Tab;
use Magento\GiftRegistry\Test\Block\Adminhtml\Customer\Edit\Tab\GiftRegistry\Grid;
use Mtf\Client\Element;

/**
 * Class GiftRegistry
 * Customer GiftRegistry edit tab
 */
class GiftRegistry extends Tab
{
    /**
     * Gift registry grid
     *
     * @var string
     */
    protected $giftRegistryGrid = '#customerGrid';

    /**
     * Get gift registry grid
     *
     * @return Grid
     */
    public function getSearchGridBlock()
    {
        return $this->blockFactory->create(
            'Magento\GiftRegistry\Test\Block\Adminhtml\Customer\Edit\Tab\GiftRegistry\Grid',
            ['element' => $this->_rootElement->find($this->giftRegistryGrid)]
        );
    }
}
