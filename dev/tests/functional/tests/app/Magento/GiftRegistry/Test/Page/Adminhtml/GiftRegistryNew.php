<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class GiftRegistryNew
 * GiftRegistry edit backend page
 */
class GiftRegistryNew extends BackendPage
{
    const MCA = 'admin/giftregistry/new';

    protected $_blocks = [
        'pageActions' => [
            'name' => 'pageActions',
            'class' => 'Magento\Backend\Test\Block\FormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'messagesBlock' => [
            'name' => 'messagesBlock',
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '#messages',
            'strategy' => 'css selector',
        ],
        'tabs' => [
            'name' => 'tabs',
            'class' => 'Magento\GiftRegistry\Test\Block\Adminhtml\Edit\Tabs',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Backend\Test\Block\FormPageActions
     */
    public function getPageActions()
    {
        return $this->getBlockInstance('pageActions');
    }

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }

    /**
     * @return \Magento\GiftRegistry\Test\Block\Adminhtml\Edit\Tabs
     */
    public function getTabs()
    {
        return $this->getBlockInstance('tabs');
    }
}
