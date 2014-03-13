<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 */
namespace Magento\Backend\Controller\Adminhtml;

use Magento\Backend\App\Action;

class Ajax extends Action
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
        /** @var \Magento\Translation\Helper\Data $translationHelper */
        $translationHelper = $this->_objectManager->get('Magento\Translation\Helper\Data');
        $response = $translationHelper->apply($translationParams, $area);
        $this->getResponse()->setBody($response);
        $this->_actionFlag->set('', self::FLAG_NO_POST_DISPATCH, true);
    }
}
