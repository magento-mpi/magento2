<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Possible data fields:
 *
 * - subject
 * - to
 * - from
 * - body
 * - template (file name)
 * - module (for template)
 *
 */
class Magento_Core_Model_Email extends Magento_Object
{
    protected $_tplVars = array();
    protected $_block;

    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig;

    /**
     * Layout factory
     *
     * @var Magento_Core_Model_LayoutFactory
     */
    protected $_layoutFactory;

    /**
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Core_Model_LayoutFactory $layoutFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Core_Model_LayoutFactory $layoutFactory,
        array $data = array()
    ) {
        $this->_layoutFactory = $layoutFactory;
        $this->_coreStoreConfig = $coreStoreConfig;
        // TODO: move to config
        $this->setFromName('Magento');
        $this->setFromEmail('magento@varien.com');
        $this->setType('text');
        parent::__construct($data);
    }

    public function setTemplateVar($var, $value = null)
    {
        if (is_array($var)) {
            foreach ($var as $index=>$value) {
                $this->_tplVars[$index] = $value;
            }
        }
        else {
            $this->_tplVars[$var] = $value;
        }
        return $this;
    }

    public function getTemplateVars()
    {
        return $this->_tplVars;
    }

    public function getBody()
    {
        $body = $this->getData('body');
        if (empty($body) && $this->getTemplate()) {
            $this->_block = $this->_layoutFactory->create()->createBlock('Magento_Core_Block_Template', 'email')
                ->setArea(Magento_Core_Model_App_Area::AREA_FRONTEND)
                ->setTemplate($this->getTemplate());
            foreach ($this->getTemplateVars() as $var=>$value) {
                $this->_block->assign($var, $value);
            }
            $this->_block->assign('_type', strtolower($this->getType()))
                ->assign('_section', 'body');
            $body = $this->_block->toHtml();
        }
        return $body;
    }

    public function getSubject()
    {
        $subject = $this->getData('subject');
        if (empty($subject) && $this->_block) {
            $this->_block->assign('_section', 'subject');
            $subject = $this->_block->toHtml();
        }
        return $subject;
    }

    public function send()
    {
        if ($this->_coreStoreConfig->getConfigFlag('system/smtp/disable')) {
            return $this;
        }

        $mail = new Zend_Mail();

        if (strtolower($this->getType()) == 'html') {
            $mail->setBodyHtml($this->getBody());
        }
        else {
            $mail->setBodyText($this->getBody());
        }

        $mail->setFrom($this->getFromEmail(), $this->getFromName())
            ->addTo($this->getToEmail(), $this->getToName())
            ->setSubject($this->getSubject());
        $mail->send();

        return $this;
    }
}
