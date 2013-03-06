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
 * Save handler for Design Tile
 *
 * @category   Mage
 * @package    Mage_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Model_Storelauncher_Design_SaveHandler
    extends Mage_Launcher_Model_Tile_ConfigBased_SaveHandlerAbstract
{
    /**
     * Helper factory
     *
     * @var Mage_Core_Model_Factory_Helper
     */
    protected $_helperFactory;

    /**
     * Config data loader
     *
     * @var Mage_Backend_Model_Config_Loader
     */
    protected $_configLoader;

    /**
     * @var Mage_Backend_Model_Config_Backend_Image_Logo
     */
    protected $_modelLogo;

    /**
     * @param Mage_Core_Model_Config $config
     * @param Mage_Backend_Model_Config $backendConfigModel
     * @param Mage_Backend_Model_Config_Loader $configLoader,
     * @param Mage_Backend_Model_Config_Backend_Image_Logo $modelLogo,
     * @param Mage_Core_Model_Factory_Helper $helperFactory
     */
    public function __construct(
        Mage_Core_Model_Config $config,
        Mage_Backend_Model_Config $backendConfigModel,
        Mage_Core_Model_Factory_Helper $helperFactory,
        Mage_Backend_Model_Config_Loader $configLoader,
        Mage_Backend_Model_Config_Backend_Image_Logo $modelLogo
    ) {
        parent::__construct($config, $backendConfigModel);
        $this->_configLoader = $configLoader;
        $this->_modelLogo = $modelLogo;
        $this->_helperFactory = $helperFactory;
    }


    /**
     * Retrieve the list of names of the related configuration sections
     *
     * @return array
     */
    public function getRelatedConfigSections()
    {
        return array('design');
    }

    /**
     * Prepare Data for system configuration
     *
     * @param array $data
     * @return array
     * @throws Mage_Launcher_Exception
     */
    public function prepareData(array $data)
    {
        if (!isset($data['groups']['design']['theme']['fields']['theme_id']['value']) ||
            !is_numeric($data['groups']['design']['theme']['fields']['theme_id']['value'])
        ) {
            throw new Mage_Launcher_Exception('Theme is required.');
        }
        /** @var $helper Mage_Launcher_Helper_Data */
        $helper = $this->_helperFactory->get('Mage_Launcher_Helper_Data');
        $store = $helper->getCurrentStoreView();
        if ($store) {
            $this->_backendConfigModel->setStore($store->getCode());
        }

        if (!empty($data['logo_src'])) {
            $file = $data['logo_src'];
            if (strrpos($file, '.tmp') == strlen($file) - 4) {
                $file = substr($file, 0, strlen($file) - 4);
            }
            $configPath = 'design/header/logo_src';
            $configScope = 'store';
            $scopeId = $store->getId();
            $config = $this->_configLoader->getConfigByPath('design/header', $configScope, $scopeId);

            $this->_modelLogo->setPath($configPath)
                ->setConfigId($config[$configPath]['config_id'])
                ->setScope($configScope)
                ->setValue(array(
                    'value' => $file,
                    'tmp_name' => $helper->getTmpLogoPath($file)
                ))
                ->save();
            unset($data['logo_src']);
        }

        return $data['groups'];
    }
}
