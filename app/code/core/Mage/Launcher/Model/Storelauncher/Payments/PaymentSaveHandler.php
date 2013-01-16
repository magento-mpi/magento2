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
 * Payment configuration save handler
 *
 * Classes that implement this interface are responsible for saving of a particular payment method configuration
 *
 * @category   Mage
 * @package    Mage_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Launcher_Model_Storelauncher_Payments_PaymentSaveHandler
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
     * Save payment configuration data
     *
     * @param array $data
     * @return null
     * @throws Mage_Launcher_Exception
     */
    abstract public function save(array $data);

    /**
     * Prepare payment configuration data for saving
     *
     * @param array $data
     * @return array prepared data
     * @throws Mage_Launcher_Exception
     */
    abstract public function prepareData(array $data);
}
