<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogEvent\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class CatalogEventIndex
 */
class CatalogEventIndex extends BackendPage
{
    const MCA = 'admin/catalog_event/index';

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
        'eventGrid' => [
            'class' => 'Magento\CatalogEvent\Test\Block\Adminhtml\Event\Grid',
            'locator' => '#catalogEventGrid',
            'strategy' => 'css selector',
        ],
        'pageActions' => [
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
     * @return \Magento\CatalogEvent\Test\Block\Adminhtml\Event\Grid
     */
    public function getEventGrid()
    {
        return $this->getBlockInstance('eventGrid');
    }

    /**
     * @return \Magento\Backend\Test\Block\GridPageActions
     */
    public function getPageActions()
    {
        return $this->getBlockInstance('pageActions');
    }
}
