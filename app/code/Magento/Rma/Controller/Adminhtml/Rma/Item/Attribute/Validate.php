<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Controller\Adminhtml\Rma\Item\Attribute;

class Validate extends \Magento\Rma\Controller\Adminhtml\Rma\Item\Attribute
{
    /**
     * Validate attribute action
     *
     * @return void
     */
    public function execute()
    {
        $response = new \Magento\Framework\Object();
        $response->setError(false);
        $attributeId = $this->getRequest()->getParam('attribute_id');
        if (!$attributeId) {
            $attributeCode = $this->getRequest()->getParam('attribute_code');
            $attributeObject = $this->_initAttribute()->loadByCode(
                $this->_getEntityType()->getId(),
                $attributeCode
            )->setCanManageOptionLabels(
                true
            );
            if ($attributeObject->getId()) {
                $this->messageManager->addError(__('An attribute with the same code already exists.'));

                $this->_view->getLayout()->initMessages();
                $response->setError(true);
                $response->setHtmlMessage($this->_view->getLayout()->getMessagesBlock()->getGroupedHtml());
            }
        }
        $this->getResponse()->representJson($response->toJson());
    }
}
