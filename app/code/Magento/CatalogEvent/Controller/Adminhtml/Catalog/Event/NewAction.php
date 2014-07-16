<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogEvent\Controller\Adminhtml\Catalog\Event;

class NewAction extends \Magento\CatalogEvent\Controller\Adminhtml\Catalog\Event
{
    /**
     * New event action
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
