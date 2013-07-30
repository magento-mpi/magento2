<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Captcha
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Captcha controller
 *
 * @category   Mage
 * @package    Mage_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Captcha_Controller_Refresh extends Mage_Core_Controller_Front_Action
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
        $captchaModel = Mage::helper('Mage_Captcha_Helper_Data')->getCaptcha($formId);
        $this->getLayout()->createBlock($captchaModel->getBlockName())->setFormId($formId)->setIsAjax(true)->toHtml();
        $this->getResponse()->setBody(json_encode(array('imgSrc' => $captchaModel->getImgSrc())));
        $this->setFlag('', self::FLAG_NO_POST_DISPATCH, true);
    }
}
