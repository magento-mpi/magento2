<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class CmsBlockIndex
 */
class CmsBlockIndex extends BackendPage
{
    const MCA = 'cms/block';

    protected $_blocks = [
        'messagesBlock' => [
            'name' => 'messagesBlock',
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '#messages',
            'strategy' => 'css selector',
        ],
        'gridPageActions' => [
            'name' => 'gridPageActions',
            'class' => 'Magento\Cms\Test\Block\Adminhtml\Block\GridPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'cmsBlockGrid' => [
            'name' => 'cmsBlockGrid',
            'class' => 'Magento\Cms\Test\Block\Adminhtml\Block\Grid',
            'locator' => '#cmsBlockGrid',
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
     * @return \Magento\Cms\Test\Block\Adminhtml\Block\GridPageActions
     */
    public function getGridPageActions()
    {
        return $this->getBlockInstance('gridPageActions');
    }

    /**
     * @return \Magento\Cms\Test\Block\Adminhtml\Block\Grid
     */
    public function getCmsBlockGrid()
    {
        return $this->getBlockInstance('cmsBlockGrid');
    }
}
