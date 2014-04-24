<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Connect\Channel;

class Generator extends \Magento\Framework\Xml\Generator
{
    /**
     * @var string
     */
    protected $_file = 'channel.xml';

    /**
     * @var \Magento\Framework\Xml\Generator|null
     */
    protected $_generator = null;

    /**
     * @param string $file
     */
    public function __construct($file = '')
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
     * @return \Magento\Framework\Xml\Generator|null
     */
    public function getGenerator()
    {
        if (is_null($this->_generator)) {
            $this->_generator = new \Magento\Framework\Xml\Generator();
        }
        return $this->_generator;
    }

    /**
     * @param array $content
     * @return $this
     */
    public function save($content)
    {
        $xmlContent = $this->getGenerator()->arrayToXml($content)->save($this->getFile());
        return $this;
    }
}
