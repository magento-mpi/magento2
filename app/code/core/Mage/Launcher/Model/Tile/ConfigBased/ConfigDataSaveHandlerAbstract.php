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
 * Configuration data save handler
 *
 * Classes that extend this class are responsible for saving of a particular part of configuration
 *
 * @category   Mage
 * @package    Mage_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Launcher_Model_Tile_ConfigBased_ConfigDataSaveHandlerAbstract
{
    /**
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    /**
     * @var Mage_Backend_Model_Config
     */
    protected $_backendConfigModel;

    /**
     * @param Mage_Core_Model_Config $config
     * @param Mage_Backend_Model_Config $backendConfigModel
     */
    public function __construct(
        Mage_Core_Model_Config $config,
        Mage_Backend_Model_Config $backendConfigModel
    ) {
        $this->_config = $config;
        $this->_backendConfigModel = $backendConfigModel;
    }


    /**
     * Save configuration data
     *
     * @param array $data
     * @return null
     * @throws Mage_Launcher_Exception
     */
    public function save(array $data)
    {
        $preparedData = $this->prepareData($data);
        $this->_backendConfigModel->setSection($this->getRelatedConfigSection())
            ->setGroups($preparedData)
            ->save();
        $this->_config->reinit();
    }

    /**
     * Retrieve name of the related configuration section
     *
     * @return string
     */
    abstract public function getRelatedConfigSection();


    /**
     * Prepare configuration data for saving
     *
     * @param array $data
     * @return array prepared data
     * @throws Mage_Launcher_Exception
     */
    abstract public function prepareData(array $data);
}
