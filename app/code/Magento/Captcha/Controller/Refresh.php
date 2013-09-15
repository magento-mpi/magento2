<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Captcha
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Captcha controller
 *
 * @category   Magento
 * @package    Magento_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Captcha\Controller;

class Refresh extends \Magento\Core\Controller\Front\Action
{
    /**
     * Refreshes captcha and returns JSON encoded URL to image (AJAX action)
     * Example: {'imgSrc': 'http://example.com/media/captcha/67842gh187612ngf8s.png'}
     *
     * @return null
     */
    public function indexAction()
    {
        $formId = $this->getRequest()->getPost('formId');
        $captchaModel = $this->_objectManager->get('Magento\Captcha\Helper\Data')->getCaptcha($formId);
        $this->getLayout()->createBlock($captchaModel->getBlockName())->setFormId($formId)->setIsAjax(true)->toHtml();
        $this->getResponse()->setBody(json_encode(array('imgSrc' => $captchaModel->getImgSrc())));
        $this->setFlag('', self::FLAG_NO_POST_DISPATCH, true);
    }
}
