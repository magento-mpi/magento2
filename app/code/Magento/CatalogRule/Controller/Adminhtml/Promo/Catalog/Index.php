<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogRule\Controller\Adminhtml\Promo\Catalog;

class Index extends \Magento\CatalogRule\Controller\Adminhtml\Promo\Catalog
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Catalog Price Rules'));

        $dirtyRules = $this->_objectManager->create('Magento\CatalogRule\Model\Flag')->loadSelf();
        if ($dirtyRules->getState()) {
            $this->messageManager->addNotice($this->getDirtyRulesNoticeMessage());
        }

        $this->_initAction()->_addBreadcrumb(__('Catalog'), __('Catalog'));
        $this->_view->renderLayout();
    }
}
