<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class AdminCache
 * Cache Management page
 */
class AdminCache extends BackendPage
{
    /**
     * URL part for cache management page
     */
    const MCA = 'admin/cache/';

    protected $_blocks = [
        'messagesBlock' => [
            'name' => 'messagesBlock',
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '#messages .messages',
            'strategy' => 'css selector',
        ],
        'actionsBlock' => [
            'name' => 'actionsBlock',
            'class' => 'Magento\Backend\Test\Block\Cache',
            'locator' => 'div.page-actions',
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
     * @return \Magento\Backend\Test\Block\Cache
     */
    public function getActionsBlock()
    {
        return $this->getBlockInstance('actionsBlock');
    }
}
