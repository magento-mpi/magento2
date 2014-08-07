<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Block\Adminhtml\Customer\Edit\Tab;

use Mtf\Client\Element;
use Magento\Backend\Test\Block\Widget\Tab;
use Magento\GiftRegistry\Test\Block\Adminhtml\Customer\Edit\Tab\GiftRegistry\Grid;

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
    protected function getSearchGridBlock()
    {
        return $this->blockFactory->create(
            'Magento\GiftRegistry\Test\Block\Adminhtml\Customer\Edit\Tab\GiftRegistry\Grid',
            ['element' => $this->_rootElement->find($this->giftRegistryGrid)]
        );
    }

    /**
     * Select gift registry
     *
     * @param array $fields
     * @param Element|null $context
     * @return $this
     */
    public function fillFormTab(array $fields, Element $context = null)
    {
        if (isset($fields['gift_registry'])) {
            $filter = [
                'title' => $fields['gift_registry']['value']['title']
            ];
            $this->getSearchGridBlock()->searchAndOpen($filter);
        }
        return $this;
    }
}
