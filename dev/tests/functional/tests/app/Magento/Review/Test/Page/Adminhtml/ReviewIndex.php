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
 *
 * @package Magento\Review\Test\Page\Adminhtml
 */
class ReviewIndex extends BackendPage
{
    const MCA = 'review/product/index';

    protected $_blocks = [
        'messagesBlock' => [
            'name' => 'messagesBlock',
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '#messages',
            'strategy' => 'css selector',
        ],
        'reviewGrid' => [
            'name' => 'reviewGrid',
            'class' => 'Magento\Review\Test\Block\Adminhtml\Grid',
            'locator' => '[id="page:main-container"]',
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
}
