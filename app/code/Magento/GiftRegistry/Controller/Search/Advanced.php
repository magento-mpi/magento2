<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Controller\Search;

class Advanced extends \Magento\GiftRegistry\Controller\Search
{
    /**
     * Load type specific advanced search attributes
     *
     * @return void
     */
    public function execute()
    {
        $this->_initType($this->getRequest()->getParam('type_id'));
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
