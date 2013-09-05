<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Menu builder command factory
 */
class Magento_Backend_Model_Menu_Builder_CommandFactory
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
     * Create new command object
     *
     * @param string $commandName
     * @param array $data
     * @return Magento_Backend_Model_Config
     */
    public function create($commandName, array $data = array())
    {
        return $this->_objectManager->
            create('Magento_Backend_Model_Menu_Builder_Command_' . ucfirst($commandName), $data);
    }
}
