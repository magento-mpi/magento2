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
 * @package     Mage_Captcha
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Captcha block
 *
 * @category   Core
 * @package    Mage_Captcha
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Captcha_Block_Captcha extends Mage_Core_Block_Template
{
    /**
     * @var string
     */
    protected $_formId;

    /**
     * @var string
     */
    protected $_imgWidth;

    /**
     * @var string
     */
    protected $_imgHeight;

    /**
     * @var string
     */
    protected $_captcha;

    /**
     * Sets form ID to which captcha is being embedded. Form ID used to get parameters exclusive for this particular
     * form and tell form's session data from each other.
     *
     * @param string $formId
     * @return Mage_Captcha_Block_Abstract
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
            Mage::throwException(Mage::helper('captcha')->__('Use setFormId action to define FormId'));
        }
        return $this->_formId;
    }

    /**
     * Sets captcha image width
     *
     * @param int $width
     * @return Mage_Captcha_Block_Abstract
     */
    public function setWidth($width)
    {
        $this->getCaptchaInstance()->setWidth($width);
        return $this;
    }

    /**
     * Sets captcha image height
     *
     * @param int $height
     * @return Mage_Captcha_Block_Abstract
     */
    public function setHeight($height)
    {
        $this->getCaptchaInstance()->setHeight($height);
        return $this;
    }

    /**
     * Returns captcha model
     *
     * @return Mage_Captcha_Model_Abstract
     */
    public function getCaptchaInstance()
    {
        if (!$this->_captcha){
            $this->_captcha = Mage::getModel('captcha/captcha', array('formId' => $this->getFormId()));
        }
        return $this->_captcha;
    }

    /**
     * Returns template path
     *
     * @return string
     */
    public function getTemplate()
    {

        $this->_template = $this->_template ? $this->_template : $this->getCaptchaInstance()->getTemplatePath();

        if ($this->getIsAjax()) {
            $this->_template = '';
        }

        return $this->_template;
    }

    /**
     * Renders captcha HTML (if required)
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (Mage::helper('captcha')->isRequired($this->getFormId())) {
            $this->getCaptchaInstance()->generate();
            return parent::_toHtml();
        }
        return '';
    }
}
