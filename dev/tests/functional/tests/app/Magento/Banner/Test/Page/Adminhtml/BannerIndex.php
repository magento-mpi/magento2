<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Banner\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class BannerIndex
 * Banner index page
 */
class BannerIndex extends BackendPage
{
    const MCA = 'admin/banner/index';

    protected $_blocks = [
        'pageActionsBlock' => [
            'name' => 'pageActionsBlock',
            'class' => 'Magento\Backend\Test\Block\GridPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'grid' => [
            'name' => 'bannerGridBlock',
            'class' => 'Magento\Banner\Test\Block\Adminhtml\Banner\Grid',
            'locator' => '#bannerGrid',
            'strategy' => 'css selector',
        ],
        'messagesBlock' => [
            'name' => 'messagesBlock',
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
     * @return \Magento\Banner\Test\Block\Adminhtml\Banner\Grid
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
