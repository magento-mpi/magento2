<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Index
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Process file entity
 */
class Magento_Index_Model_Process_FileFactory
{
    /**
     * Entity class name
     */
    const CLASS_NAME = 'Magento_Index_Model_Process_File';

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
     * @param array $arguments
     * @return Magento_Index_Model_Process_File
     */
    public function create(array $arguments = array())
    {
        return $this->_objectManager->create(self::CLASS_NAME, $arguments);
    }
}
