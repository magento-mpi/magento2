<?php
/**
 * DB store configuration data converter. Converts associative array to tree array
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Config\Scope\Store;

class Converter extends \Magento\Core\Model\Config\Scope\Converter
{
    /**
     * @var \Magento\Core\Model\Config\Scope\Processor\Placeholder
     */
    protected $_processor;

    /**
     * @param \Magento\Core\Model\Config\Scope\Processor\Placeholder $processor
     */
    public function __construct(\Magento\Core\Model\Config\Scope\Processor\Placeholder $processor)
    {
        $this->_processor = $processor;
    }

    /**
     * Convert config data
     *
     * @param array $source
     * @param array $initialConfig
     * @return array
     */
    public function convert($source, $initialConfig = array())
    {
        $storeConfig = array_replace_recursive($initialConfig, parent::convert($source));
        return $this->_processor->process($storeConfig);
    }
}
