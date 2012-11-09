<?php
/**
 * Process file entity
 *
 * @copyright {}
 */
class Mage_Index_Model_Process_FileFactory implements Magento_ObjectManager_Factory
{
    /**
     * Entity class name
     */
    const CLASS_NAME = 'Mage_Index_Model_Process_File';

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
     * @return Mage_Index_Model_Process_File
     */
    public function createFromArray(array $arguments = array())
    {
        return $this->_objectManager->create(self::CLASS_NAME, $arguments, false);
    }
}
