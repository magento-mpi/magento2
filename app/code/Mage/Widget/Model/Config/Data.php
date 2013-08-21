<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Widget_Model_Config_Data extends Magento_Config_Data
{
    /**
     * Scope priority loading scheme
     *
     * @var array
     */
    protected $_scopePriorityScheme = array('global');

    /**
     * @param Mage_Widget_Model_Config_Reader $reader
     * @param Magento_Config_ScopeInterface $configScope
     * @param Magento_Config_CacheInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        Mage_Widget_Model_Config_Reader $reader,
        Magento_Config_ScopeInterface $configScope,
        Magento_Config_CacheInterface $cache,
        $cacheId
    ) {
        parent::__construct($reader, $configScope, $cache, $cacheId);
    }

    /**
     * Get attributes
     *
     * @param array $data
     * @return null|array
     */
    public function getAttributes(array $data)
    {
        if (isset($data[0])) {
            return isset($data[0][Magento_Config_Converter_Dom::ATTRIBUTES])
                ? $data[0][Magento_Config_Converter_Dom::ATTRIBUTES]
                : null;
        }
        return null;
    }

    /**
     * Get content
     *
     * @param array $data
     * @return null|array
     */
    public function getContent(array $data)
    {
        if (isset($data[0])) {
            return isset($data[0][Magento_Config_Converter_Dom::CONTENT])
                ? $data[0][Magento_Config_Converter_Dom::CONTENT]
                : null;
        }
        return null;
    }
}
