<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Captcha
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Captcha\Controller\Adminhtml;

/**
 * Captcha controller
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Refresh extends \Magento\Backend\App\Action
{
    /**
     * Refreshes captcha and returns JSON encoded URL to image (AJAX action)
     * Example: {'imgSrc': 'http://example.com/media/captcha/67842gh187612ngf8s.png'}
     *
     * @return void
     */
    public function refreshAction()
    {
        $formId = $this->getRequest()->getPost('formId');
        $captchaModel = $this->_objectManager->get('Magento\Captcha\Helper\Data')->getCaptcha($formId);
        $this->_view->getLayout()
            ->createBlock($captchaModel->getBlockName())
            ->setFormId($formId)
            ->setIsAjax(true)
            ->toHtml();
        $this->getResponse()->setBody(json_encode(array('imgSrc' => $captchaModel->getImgSrc())));
        $this->_actionFlag->set('', self::FLAG_NO_POST_DISPATCH, true);
    }
}
