<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogEvent\Controller\Adminhtml\Catalog\Event;

class Index extends \Magento\CatalogEvent\Controller\Adminhtml\Catalog\Event
{
    /**
     * Events list action
     *
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Events'));
        $this->_initAction();
        $this->_view->renderLayout();
    }
}
