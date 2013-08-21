<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Mage_Widget_Model_Config_Converter implements Magento_Config_ConverterInterface
{
    /** @var  Magento_Simplexml_Config */
    protected $_factory;

    /**
     * Constructor
     *
     * @param Magento_Simplexml_Config_Factory $factory
     */
    public function __construct(
        Magento_Simplexml_Config_Factory $factory
    ) {
        $this->_factory = $factory;
    }

    /**
     * Convert dom node tree to magneto xml config string
     *
     * @param DOMDocument $source
     * @return array
     */
    public function convert($source)
    {
        /** @var Magento_Simplexml_Config $nodeListData */
        $nodeListData = $this->_factory->create();
        $nodeListData->loadDom($source);

        return $nodeListData->getNode()->asArray();
    }
}
