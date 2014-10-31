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
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $this->_title->add(__('Store Design'));

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magento_Backend::system_design_schedule');

        $id = (int)$this->getRequest()->getParam('id');
        $design = $this->_objectManager->create('Magento\Framework\App\DesignInterface');

        if ($id) {
            $design->load($id);
        }

        $this->_title->add($design->getId() ? __('Edit Store Design Change') : __('New Store Design Change'));

        $this->_coreRegistry->register('design', $design);

        $resultPage->addContent($resultPage->getLayout()->createBlock('Magento\Backend\Block\System\Design\Edit'));
        $resultPage->addLeft(
            $resultPage->getLayout()->createBlock('Magento\Backend\Block\System\Design\Edit\Tabs', 'design_tabs')
        );

        return $resultPage;
    }
}
