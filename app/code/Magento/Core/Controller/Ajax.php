<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 */
namespace Magento\Core\Controller;

class Ajax extends \Magento\App\Action\Action
{
    /**
     * Ajax action for inline translation
     *
     * @return void
     */
    public function translateAction()
    {
        $translationParams = (array)$this->getRequest()->getPost('translate');
        $area = $this->getRequest()->getPost('area');
        /** @var \Magento\Translate\Helper\Data $translationHelper */
        $translationHelper = $this->_objectManager->get('Magento\Translate\Helper\Data');
        $response = $translationHelper->apply($translationParams, $area);
        $this->getResponse()->setBody($response);
        $this->_actionFlag->set('', self::FLAG_NO_POST_DISPATCH, true);
    }
}
