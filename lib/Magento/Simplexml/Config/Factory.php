<?php
/**
 * Creates new simplexml config
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Simplexml_Config_Factory
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * initialize the class
     *
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(
        Magento_ObjectManager $objectManager
    ) {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create simplexml config
     *
     * @param string|Magento_Simplexml_Element $sourceData
     *
     * @return Magento_Outbound_Message
     */
    public function create($sourceData=null)
    {
        return $this->_objectManager->create(
            'Magento_Simplexml_Config',
            array('sourceData' => $sourceData));
    }
}