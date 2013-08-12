<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Connect_Channel_Generator extends Magento_Xml_Generator
{
    protected $_file      = 'channel.xml';
    protected $_generator = null;

    public function __construct($file='')
    {
        if ($file) {
            $this->_file = $file;
        }
        return $this;
    }

    public function getFile()
    {
        return $this->_file;
    }

    public function getGenerator()
    {
        if (is_null($this->_generator)) {
            $this->_generator = new Magento_Xml_Generator();
        }
        return $this->_generator;
    }

    /**
     * @param array $content
     */
    public function save($content)
    {
        $xmlContent = $this->getGenerator()
        ->arrayToXml($content)
        ->save($this->getFile());
        return $this;
    }
}
