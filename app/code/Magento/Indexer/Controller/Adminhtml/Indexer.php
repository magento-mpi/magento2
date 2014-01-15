<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Index
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Indexer\Controller\Adminhtml;

class Indexer extends \Magento\Backend\App\Action
{
    /**
     * Display processes grid action
     */
    public function listAction()
    {
        $this->_title->add(__('New Index Management'));

        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Indexer::system_index');
        $this->_view->renderLayout();
    }

    /**
     * Check ACL permissions
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Indexer::index');
    }
}
