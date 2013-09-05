<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Log_Model_Shell_Command_Factory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create clean command
     *
     * @param int $days
     * @return Magento_Log_Model_Shell_CommandInterface
     */
    public function createCleanCommand($days)
    {
        return $this->_objectManager->create('Magento_Log_Model_Shell_Command_Clean', array('days' => $days));
    }

    /**
     * Create status command
     *
     * @return Magento_Log_Model_Shell_CommandInterface
     */
    public function createStatusCommand()
    {
        return $this->_objectManager->create('Magento_Log_Model_Shell_Command_Status');
    }
}