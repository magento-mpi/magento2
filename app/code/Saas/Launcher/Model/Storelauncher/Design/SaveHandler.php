<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Saas_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Save handler for Design Tile
 *
 * @category   Mage
 * @package    Saas_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Saas_Launcher_Model_Storelauncher_Design_SaveHandler
    extends Saas_Launcher_Model_Tile_ConfigBased_SaveHandlerAbstract
{
    const XML_PATH_LOGO = 'design/header/logo_src';

    /**
     * Helper factory
     *
     * @var Mage_Core_Model_Factory_Helper
     */
    protected $_helperLauncher;

    /**
     * Config data loader
     *
     * @var Mage_Backend_Model_Config_Loader
     */
    protected $_configLoader;

    /**
     * Config Writer Model
     *
     * @var Mage_Core_Model_Config_Storage_WriterInterface
     */
    protected $_configWriter;

    /**
     * @var Mage_Backend_Model_Config_Backend_Image_Logo
     */
    protected $_modelLogo;


    /**
     * @var Mage_Theme_Model_Config
     */
    protected $_themeConfig;

    /**
     * @param Mage_Core_Model_Config $config
     * @param Mage_Backend_Model_Config $backendConfigModel
     * @param Mage_Backend_Model_Config_Loader $configLoader
     * @param Mage_Core_Model_Config_Storage_WriterInterface $configWriter
     * @param Mage_Backend_Model_Config_Backend_Image_Logo $modelLogo
     * @param Saas_Launcher_Helper_Data $helperLauncher
     * @param Mage_Theme_Model_Config $themeConfig
     */
    public function __construct(
        Mage_Core_Model_Config $config,
        Mage_Backend_Model_Config $backendConfigModel,
        Saas_Launcher_Helper_Data $helperLauncher,
        Mage_Backend_Model_Config_Loader $configLoader,
        Mage_Core_Model_Config_Storage_WriterInterface $configWriter,
        Mage_Backend_Model_Config_Backend_Image_Logo $modelLogo,
        Mage_Theme_Model_Config $themeConfig
    ) {
        parent::__construct($config, $backendConfigModel);
        $this->_configLoader = $configLoader;
        $this->_configWriter = $configWriter;
        $this->_modelLogo = $modelLogo;
        $this->_helperLauncher = $helperLauncher;
        $this->_themeConfig = $themeConfig;
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
     * @throws Saas_Launcher_Exception
     */
    public function prepareData(array $data)
    {
        if (!isset($data['groups']['design']['theme']['fields']['theme_id']['value']) ||
            !is_numeric($data['groups']['design']['theme']['fields']['theme_id']['value'])
        ) {
            throw new Saas_Launcher_Exception('Theme is required.');
        }
        $store = $this->_helperLauncher->getCurrentStoreView();
        $theme = $this->_themeConfig->assignToStore(
            $data['groups']['design']['theme']['fields']['theme_id']['value'],
            array($this->_helperLauncher->getCurrentStoreView()->getId())
        );
        $data['groups']['design']['theme']['fields']['theme_id']['value'] = $theme->getId();

        if (!empty($data['logo_src'])) {
            $file = $data['logo_src'];
            if (strrpos($file, '.tmp') == strlen($file) - 4) {
                $file = substr($file, 0, strlen($file) - 4);
            }
            $configPath = self::XML_PATH_LOGO;
            $configScope = Mage_Core_Model_Config::SCOPE_STORES;
            $scopeId = $store->getId();
            $config = $this->_configLoader->getConfigByPath('design/header', $configScope, $scopeId);

            if (empty($config[$configPath]['config_id'])) {
                $this->_configWriter->save($configPath, '', $configScope, $scopeId);
                $config = $this->_configLoader->getConfigByPath('design/header', $configScope, $scopeId);
            }
            if (!empty($config[$configPath]['config_id'])) {
                $this->_modelLogo->setConfigId($config[$configPath]['config_id']);
            }
            $this->_modelLogo->setPath($configPath)
                ->setScope($configScope)
                ->setScopeId($scopeId)
                ->setValue(array(
                    'value' => $file,
                    'tmp_name' => $this->_helperLauncher->getTmpLogoPath($file)
                ))
                ->save();
            unset($data['logo_src']);
        }

        return $data['groups'];
    }
}
