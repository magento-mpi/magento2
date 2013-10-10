<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Connect\Channel;

class Generator extends \Magento\Xml\Generator
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
            $this->_generator = new \Magento\Xml\Generator();
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
