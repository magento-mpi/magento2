<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Zend captcha block
 *
 * @category   Mage
 * @package    Mage_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Block_Captcha_Zend extends Mage_Core_Block_Template
{
    const DEFAULT_TEMPLATE = 'captcha/zend.phtml';
    /* @var Mage_Core_Model_Captcha_Zend */
    protected $_captcha = null;
    protected $_formId;
    protected $_imgWidth;
    protected $_imgHeight;

    /**
     * Returns template path
     *
     * @return string
     */
    public function getTemplate()
    {
        parent::getTemplate() || $this->setTemplate(self::DEFAULT_TEMPLATE);
        return parent::getTemplate();
    }

    /**
     * Sets instance of a model used to generate captcha
     *
     * @param Mage_Core_Model_Captcha_Zend $captcha
     * @return Mage_Core_Block_Captcha_Zend
     */
    public function setCaptchaInstance(Mage_Core_Model_Captcha_Zend $captcha)
    {
        $this->_captcha = $captcha;
        return $this;
    }

    /**
     * Returns captcha model
     *
     * @return Mage_Core_Model_Captcha_Zend
     */
    public function getCaptchaInstance()
    {
        if (!$this->_captcha) {
            /* @var $captcha Mage_Core_Model_Captcha_Zend */
            $captcha = Mage::getModel('core/captcha_zend');
            $this->setCaptchaInstance($captcha);
        }
        return $this->_captcha;
    }

    /**
     * Sets form ID to which captcha is being embedded. Form ID used to get parameters exclusive for this particular
     * form and tell form's session data from each other.
     *
     * @param string $formId
     * @return Mage_Core_Block_Captcha_Zend
     */
    public function setFormId($formId)
    {
        $this->_formId = $formId;
        return $this;
    }

    /**
     * Returns current form ID assigned
     *
     * @return string
     */
    public function getFormId()
    {
        if (empty($this->_formId)) {
            Mage::throwException(Mage::helper('core/captcha')->__('Use setFormId action to define FormId'));
        }
        return $this->_formId;
    }

    /**
     * Renders captcha image HTML
     *
     * @return string
     */
    public function render()
    {
        $captcha = $this->getCaptchaInstance();
        $html = '<img width="' . $captcha->getWidth() . '" height="' . $captcha->getHeight() . '" alt="'
                . $captcha->getImgAlt() . '" src="' . $captcha->getImgUrl() . $captcha->getId() . $captcha->getSuffix()
                . '"/>';
        return $html;
    }

    /**
     * Sets captcha image width
     *
     * @param int $width
     * @return Mage_Core_Block_Captcha_Zend
     */
    public function setImgWidth($width)
    {
        $this->getCaptchaInstance()->setWidth($width);
        return $this;
    }

    /**
     * Sets captcha image height
     *
     * @param int $height
     * @return Mage_Core_Block_Captcha_Zend
     */
    public function setImgHeight($height)
    {
        $this->getCaptchaInstance()->setHeight($height);
        return $this;
    }

    /**
     * Renders captcha HTML (if required)
     *
     * @return string
     */
    protected function _toHtml()
    {
        /* @var $helper  Mage_Core_Helper_Captcha */
        $helper = Mage::helper('core/captcha');
        if ($helper->isRequired($this->getFormId())) {
            $this->getCaptchaInstance()->generate();
            return parent::_toHtml();
        }
        return '';
    }
}
