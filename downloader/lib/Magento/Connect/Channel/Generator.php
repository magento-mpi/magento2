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
    /**
     * @var string
     */
    protected $_file      = 'channel.xml';

    /**
     * @var \Magento\Xml\Generator|null
     */
    protected $_generator = null;

    /**
     * @param string $file
     */
    public function __construct($file='')
    {
        if ($file) {
            $this->_file = $file;
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->_file;
    }

    /**
     * @return \Magento\Xml\Generator|null
     */
    public function getGenerator()
    {
        if (is_null($this->_generator)) {
            $this->_generator = new \Magento\Xml\Generator();
        }
        return $this->_generator;
    }

    /**
     * @param array $content
     * @return $this
     */
    public function save($content)
    {
        $xmlContent = $this->getGenerator()
        ->arrayToXml($content)
        ->save($this->getFile());
        return $this;
    }
}
