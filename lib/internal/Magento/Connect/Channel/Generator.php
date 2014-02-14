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

use Magento\Xml\Generator as XmlGenerator;

class Generator extends XmlGenerator
{
    /**
     * @var string
     */
    protected $_file = 'channel.xml';

    /**
     * @var XmlGenerator
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
     * @return XmlGenerator
     */
    public function getGenerator()
    {
        if (is_null($this->_generator)) {
            $this->_generator = new XmlGenerator();
        }
        return $this->_generator;
    }

    /**
     * @param array $content
     * @return this
     */
    public function save($content)
    {
        $xmlContent = $this->getGenerator()
        ->arrayToXml($content)
        ->save($this->getFile());
        return $this;
    }
}
