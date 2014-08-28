<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml\System\Design;

class Edit extends \Magento\Backend\Controller\Adminhtml\System\Design
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Store Design'));

        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Backend::system_design_schedule');
//        $this->_view->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $id = (int)$this->getRequest()->getParam('id');
        $design = $this->_objectManager->create('Magento\Framework\App\DesignInterface');

        if ($id) {
            $design->load($id);
        }

        $this->_title->add($design->getId() ? __('Edit Store Design Change') : __('New Store Design Change'));

        $this->_coreRegistry->register('design', $design);

        $this->_addContent($this->_view->getLayout()->createBlock('Magento\Backend\Block\System\Design\Edit'));
        $this->_addLeft(
            $this->_view->getLayout()->createBlock('Magento\Backend\Block\System\Design\Edit\Tabs', 'design_tabs')
        );

        $this->_view->renderLayout();
    }
}
