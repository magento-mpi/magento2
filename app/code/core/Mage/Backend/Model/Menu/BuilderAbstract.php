<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Backend_Model_Menu_BuilderAbstract
{
    /**
     * @var Mage_Backend_Model_Menu_Builder_CommandAbstract[]
     */
    protected $_commands = array();

    /**
     * Process provided command object
     *
     * @param Mage_Backend_Model_Menu_Builder_CommandAbstract $command
     * @return Mage_Backend_Model_Menu_BuilderAbstract
     */
    public function processCommand(Mage_Backend_Model_Menu_Builder_CommandAbstract $command)
    {
        $command->chain(isset($this->_commands[$command->getId()]) ? $this->_commands[$command->getId()] : null);
        if (isset($this->_commands[$command->getId()])) {
            $this->_commands[$command->getId()] = $command;
        } else {
            $this->_commands[$command->getId()]->chain($command);
        }
        return $this;
    }

    /**
     * @return Varien_Simplexml_Config
     */
    public function getResult()
    {
        foreach ($this->_commands as $command) {
            $command->execute(Mage::getModel('Mage_Backend_Model_Menu_Item'));
        }

    }
}
