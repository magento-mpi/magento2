<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
