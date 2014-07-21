<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogEvent\Controller\Adminhtml\Catalog\Event;

class CategoriesJson extends \Magento\CatalogEvent\Controller\Adminhtml\Catalog\Event
{
    /**
     * Ajax categories tree loader action
     *
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id', null);
        $this->getResponse()->representJson(
            $this->_view->getLayout()->createBlock(
                'Magento\CatalogEvent\Block\Adminhtml\Event\Edit\Category'
            )->getTreeArray(
                $id,
                true,
                1
            )
        );
    }
}
