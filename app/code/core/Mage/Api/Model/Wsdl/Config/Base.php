<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Api
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Wsdl base config
 *
 * @category   Mage
 * @package    Mage_Api
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api_Model_Wsdl_Config_Base extends Varien_Simplexml_Config
{
    protected $_handler = '';

    /**
     * @var Varien_Object
     */
    protected $_wsdlVariables = null;

    protected $_loadedFiles = array();

    public function __construct($sourceData=null)
    {
        $this->_elementClass = 'Mage_Api_Model_Wsdl_Config_Element';
        parent::__construct($sourceData);
    }

    /**
     * Set handler
     *
     * @param string $handler
     * @return Mage_Api_Model_Wsdl_Config_Base
     */
    public function setHandler($handler)
    {
        $this->_handler = $handler;
        return $this;
    }

    /**
     * Get handler
     *
     * @return string
     */
    public function getHandler()
    {
        return $this->_handler;
    }

    /**
     * Processing file data
     *
     * @param string $text
     * @return string
     */
    public function processFileData($text)
    {
        $template = Mage::getModel('Mage_Core_Model_Email_Template_Filter');

        if (null === $this->_wsdlVariables) {
            $this->_wsdlVariables = new Varien_Object();
            $this->_wsdlVariables->setUrl(Mage::getUrl('*/*/*'));
            $this->_wsdlVariables->setName('Magento');
            $this->_wsdlVariables->setHandler($this->getHandler());
        }

        $template->setVariables(array('wsdl'=>$this->_wsdlVariables));

        $text = $template->filter($text);

        return $text;
    }

    public function addLoadedFile($file)
    {
        if (!in_array($file, $this->_loadedFiles)) {
            $this->_loadedFiles[] = $file;
        }
        return $this;
    }

    public function loadFile($file)
    {
        if (in_array($file, $this->_loadedFiles)) {
            return false;
        }
        $res = parent::loadFile($file);
        if ($res) {
            $this->addLoadedFile($file);
        }
        return $this;
    }
}
