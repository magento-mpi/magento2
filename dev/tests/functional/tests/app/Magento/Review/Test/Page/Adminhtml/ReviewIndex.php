<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Review\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class ReviewIndex
 */
class ReviewIndex extends BackendPage
{
    const MCA = 'review/product/index';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'messagesBlock' => [
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '#messages',
            'strategy' => 'css selector',
        ],
        'reviewGrid' => [
            'class' => 'Magento\Review\Test\Block\Adminhtml\Grid',
            'locator' => '#reviwGrid',
            'strategy' => 'css selector',
        ],
        'reviewActions' => [
            'class' => 'Magento\Backend\Test\Block\GridPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }

    /**
     * @return \Magento\Review\Test\Block\Adminhtml\Grid
     */
    public function getReviewGrid()
    {
        return $this->getBlockInstance('reviewGrid');
    }

    /**
     * @return \Magento\Backend\Test\Block\GridPageActions
     */
    public function getReviewActions()
    {
        return $this->getBlockInstance('reviewActions');
    }
}
