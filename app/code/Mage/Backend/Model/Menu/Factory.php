<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Backend_Model_Menu_Factory
{
    /**
     * @var Magento_Core_Model_Logger
     */
    protected $_logger;

    /**
     * @var Magento_ObjectManager
     */
    protected $_factory;

    /**
     * @param Magento_ObjectManager $factory
     * @param Magento_Core_Model_Logger $logger
     */
    public function __construct(Magento_ObjectManager $factory, Magento_Core_Model_Logger $logger)
    {
        $this->_factory = $factory;
        $this->_logger = $logger;
    }

    /**
     * Retrieve menu model
     * @param string $path
     * @return Mage_Backend_Model_Menu
     */
    public function getMenuInstance($path = '')
    {
        return $this->_factory->create(
            'Mage_Backend_Model_Menu', array('menuLogger' => $this->_logger, 'pathInMenuStructure' => $path)
        );
    }
}
