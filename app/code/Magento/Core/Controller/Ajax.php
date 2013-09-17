<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 */
class Magento_Core_Controller_Ajax extends Magento_Core_Controller_Front_Action
{
    /**
     * Ajax action for inline translation
     */
    public function translateAction()
    {
        $translationParams = (array)$this->getRequest()->getPost('translate');
        $area = $this->getRequest()->getPost('area');
        /** @var Magento_Core_Helper_Translate $translationHelper */
        $translationHelper = $this->_objectManager->get('Magento_Core_Helper_Translate');
        $response = $translationHelper->apply($translationParams, $area);
        $this->getResponse()->setBody($response);
        $this->setFlag('', self::FLAG_NO_POST_DISPATCH, true);
    }
}
