<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 */
namespace Magento\Backend\Controller\Adminhtml;

class Ajax extends \Magento\Backend\Controller\Adminhtml\Action
{
    /**
     * Ajax action for inline translation
     */
    public function translateAction()
    {
        $translationParams = (array)$this->getRequest()->getPost('translate');
        $area = $this->getRequest()->getPost('area');
        /** @var \Magento\Core\Helper\Translate $translationHelper */
        $translationHelper = $this->_objectManager->get('Magento\Core\Helper\Translate');
        $response = $translationHelper->apply($translationParams, $area);
        $this->getResponse()->setBody($response);
        $this->setFlag('', self::FLAG_NO_POST_DISPATCH, true);
    }
}
