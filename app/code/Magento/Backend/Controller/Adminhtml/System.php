<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * System admin controller
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Controller\Adminhtml;

class System extends \Magento\Backend\App\AbstractAction
{
    public function indexAction()
    {
        $this->_layoutServices->loadLayout();
        $this->_setActiveMenu('Magento_Adminhtml::system');
        $this->_addBreadcrumb(
            __('System'),
            __('System')
        );
        $this->renderLayout();
    }

    public function setStoreAction()
    {
        $storeId = (int) $this->getRequest()->getParam('store');
        if ($storeId) {
            $this->_session->setStoreId($storeId);
        }
        $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl($this->getUrl('*')));
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Adminhtml::system');
    }
}
