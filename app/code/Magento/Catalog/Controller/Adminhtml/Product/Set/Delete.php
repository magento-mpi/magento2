<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller\Adminhtml\Product\Set;

class Delete extends \Magento\Catalog\Controller\Adminhtml\Product\Set
{
    /**
     * @return void
     */
    public function execute()
    {
        $setId = $this->getRequest()->getParam('id');
        try {
            $this->_objectManager->create('Magento\Eav\Model\Entity\Attribute\Set')->setId($setId)->delete();

            $this->messageManager->addSuccess(__('The attribute set has been removed.'));
            $this->getResponse()->setRedirect($this->getUrl('catalog/*/'));
        } catch (\Exception $e) {
            $this->messageManager->addError(__('An error occurred while deleting this set.'));
            $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl($this->getUrl('*')));
        }
    }
}
