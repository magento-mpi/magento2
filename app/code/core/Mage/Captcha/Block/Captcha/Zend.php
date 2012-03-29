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
 * Captcha block
 *
 * @category   Core
 * @package    Mage_Captcha
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Captcha_Block_Captcha_Zend extends Mage_Core_Block_Template
{
    protected $_template = 'zend.phtml';

    /**
     * @var string
     */
    protected $_captcha;

    /**
     * Returns template path
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->getIsAjax() ? '' : $this->_template;
    }

    /**
     * Returns URL to controller action which returns new captcha image
     *
     * @return string
     */
    public function getRefreshUrl()
    {
        $isSecure = Mage::app()->getStore()->isAdmin()
            ? Mage::app()->getStore()->isAdminUrlSecure()
            : Mage::getConfig()->shouldUrlBeSecure(Mage::app()->getRequest()->getPathInfo());
        return Mage::getUrl(
            Mage::app()->getStore()->isAdmin() ? 'adminhtml/refresh/refresh' : 'captcha/refresh',
            array('_secure' => $isSecure)
        );
    }

    /**
     * Renders captcha HTML (if required)
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->getCaptchaModel()->isRequired()) {
            $this->getCaptchaModel()->generate();
            return parent::_toHtml();
        }
        return '';
    }

    /**
     * Returns captcha model
     *
     * @return Mage_Captcha_Model_Abstract
     */
    public function getCaptchaModel()
    {
        return Mage::helper('Mage_Captcha_Helper_Data')->getCaptcha($this->getFormId());
    }
}
