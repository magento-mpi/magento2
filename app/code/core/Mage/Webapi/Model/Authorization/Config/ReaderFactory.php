<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Factory for Mage_Webapi_Model_Authorization_Config_Reader
 */
class Mage_Webapi_Model_Authorization_Config_ReaderFactory
{
    const READER_CLASS_NAME = 'Mage_Webapi_Model_Authorization_Config_Reader';

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Return new Mage_Webapi_Model_Authorization_Config_Reader
     *
     * @param array $arguments
     * @return Mage_Webapi_Model_Authorization_Config_Reader
     */
    public function createReader(array $arguments = array())
    {
        return $this->_objectManager->create(self::READER_CLASS_NAME, $arguments, false);
    }
}
