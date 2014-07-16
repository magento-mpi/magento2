<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Banner\Controller\Adminhtml\Banner;

class SalesRuleGrid extends \Magento\Banner\Controller\Adminhtml\Banner
{
    /**
     * Banner sales rule grid action on promotions tab
     * Load banner by ID from post data
     * Register banner model
     *
     * @return void
     */
    public function execute()
    {
        $bannerId = $this->getRequest()->getParam('id');
        $model = $this->_initBanner('id');

        if (!$model->getId() && $bannerId) {
            $this->messageManager->addError(__('This banner does not exist.'));
            $this->_redirect('adminhtml/*/');
            return;
        }

        $this->_view->loadLayout();
        $this->_view->getLayout()->getBlock(
            'banner_salesrule_grid'
        )->setSelectedSalesRules(
            $this->getRequest()->getPost('selected_salesrules')
        );
        $this->_view->renderLayout();
    }
}
