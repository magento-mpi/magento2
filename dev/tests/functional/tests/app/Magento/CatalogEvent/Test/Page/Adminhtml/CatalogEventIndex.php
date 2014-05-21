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
 * Class AdminCatalogEventIndex
 * Catalog Event page
 */
class CatalogEventIndex extends BackendPage
{
    const MCA = 'admin/catalog_event/index';

    protected $_blocks = [
        'messageBlock' => [
            'name' => 'messageBlock',
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '#messages',
            'strategy' => 'css selector',
        ],
        'Grid' => [
            'name' => 'blockEventGrid',
            'class' => 'Magento\CatalogEvent\Test\Block\Adminhtml\Event\Grid',
            'locator' => '#catalogEventGrid',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessageBlock()
    {
        return $this->getBlockInstance('messageBlock');
    }

    /**
     * @return \Magento\CatalogEvent\Test\Block\Adminhtml\Event\Grid
     */
    public function getBlockEventGrid()
    {
        return $this->getBlockInstance('Grid');
    }
}
