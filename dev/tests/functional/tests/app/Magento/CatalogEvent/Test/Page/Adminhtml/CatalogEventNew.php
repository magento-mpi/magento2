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
 * Class AdminCatalogEventNew
 * Add new Catalog Event page
 */
class CatalogEventNew extends BackendPage
{
    const MCA = 'admin/catalog_event/new';

    protected $_blocks = [
        'pageActions' => [
            'name' => 'pageActions',
            'class' => 'Magento\Backend\Test\Block\FormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'eventForm' => [
            'name' => 'eventForm',
            'class' => 'Magento\CatalogEvent\Test\Block\Adminhtml\Event\Edit\Form',
            'locator' => '#edit_form',
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
     * @return \Magento\CatalogEvent\Test\Block\Adminhtml\Event\Edit\Form
     */
    public function getEventForm()
    {
        return $this->getBlockInstance('eventForm');
    }
}
