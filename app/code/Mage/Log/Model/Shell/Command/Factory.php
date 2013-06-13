<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Log_Model_Shell_Command_Factory
{
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
     * Create clean command
     *
     * @param int $days
     * @return Mage_Log_Model_Shell_CommandInterface
     */
    public function createCleanCommand($days)
    {
        return $this->_objectManager->create('Mage_Log_Model_Shell_Command_Clean', array('days' => $days));
    }

    /**
     * Create status command
     *
     * @return Mage_Log_Model_Shell_CommandInterface
     */
    public function createStatusCommand()
    {
        return $this->_objectManager->create('Mage_Log_Model_Shell_Command_Status');
    }
}