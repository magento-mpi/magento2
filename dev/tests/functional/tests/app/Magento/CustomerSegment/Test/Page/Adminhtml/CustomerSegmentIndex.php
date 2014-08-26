<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerSegment\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class CustomerSegmentIndex
 */
class CustomerSegmentIndex extends BackendPage
{
    const MCA = 'customersegment/index';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'pageActionsBlock' => [
            'class' => 'Magento\Backend\Test\Block\GridPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'grid' => [
            'class' => 'Magento\CustomerSegment\Test\Block\Adminhtml\Customersegment\Grid',
            'locator' => '#customersegmentGrid',
            'strategy' => 'css selector',
        ],
        'messagesBlock' => [
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '.messages .messages',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Backend\Test\Block\GridPageActions
     */
    public function getPageActionsBlock()
    {
        return $this->getBlockInstance('pageActionsBlock');
    }

    /**
     * @return \Magento\CustomerSegment\Test\Block\Adminhtml\Customersegment\Grid
     */
    public function getGrid()
    {
        return $this->getBlockInstance('grid');
    }

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }
}
