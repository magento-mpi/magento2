<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Configuration save handler factory
 *
 * @category   Mage
 * @package    Mage_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Launcher_Model_Tile_ConfigBased_SaveHandlerFactoryAbstract
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
     * Create new configuration save handler based on given save handler ID
     *
     * @param string $saveHandlerId
     * @param array $arguments
     * @return Mage_Launcher_Model_Tile_ConfigBased_SaveHandlerAbstract
     * @throws Mage_Launcher_Exception
     */
    public function create($saveHandlerId, array $arguments = array())
    {
        $saveHandlerMap = $this->getSaveHandlerMap();
        if (!isset($saveHandlerMap[$saveHandlerId])) {
            throw new Mage_Launcher_Exception('Illegal configuration save handler ID specified.');
        }
        return $this->_objectManager->create($saveHandlerMap[$saveHandlerId], $arguments);
    }

    /**
     * Retrieve save handler ID - save handler class name map
     *
     * @return array
     */
    abstract public function getSaveHandlerMap();
}
