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
 * @method Mage_Core_Block_Captcha_Zend setIsAjax()
 * @method bool                         getIsAjax()
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
     * Returns template path
     *
     * @return string
     */
    public function getTemplate()
    {
        if ($this->getIsAjax()) {
            $this->setTemplate('');
        } else {
            parent::getTemplate() || $this->setTemplate(self::DEFAULT_TEMPLATE);
        }
        return parent::getTemplate();
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
            $captcha = Mage::getModel('core/captcha_zend', $this->getFormId());
            $this->setCaptchaInstance($captcha);
        }
        return $this->_captcha;
    }




    /**
     * Renders captcha image HTML
     *
     * @return string
     */
    public function render()
    {
        $captcha = $this->getCaptchaInstance();
        $html = '<img id="' . $this->getFormId() . '" width="' . $captcha->getWidth() . '" height="'
                . $captcha->getHeight() . '" alt="' . $captcha->getImgAlt() . '" src="' . $this->getImgSrc() . '"/>';
        return $html;
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

    /**
     * Return full URL to captcha image
     *
     * @return string
     */
    public function getImgSrc()
    {
        $captcha = $this->getCaptchaInstance();
        return $captcha->getImgUrl() . $captcha->getId() . $captcha->getSuffix();
    }
}
