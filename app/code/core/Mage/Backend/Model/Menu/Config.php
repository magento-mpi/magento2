<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Backend_Model_Menu_Config
{
    const CACHE_ID = 'backend_menu_config';

    /**
     * @var Mage_Core_Model_Cache
     */
    protected $_cache;

    /**
     * @var Mage_Core_Model_Config
     */
    protected $_appConfig;

    /**
     * @var Mage_Backend_Model_Auth_Session
     */
    protected $_acl;

    /**
     * @var Mage_Backend_Model_Url
     */
    protected $_urlModel;

    /**
     * @var Mage_Backend_Model_Menu_Item_Validator
     */
    protected $_itemValidator;

    /**
     * Menu model
     *
     * @var Mage_Backend_Model_Menu
     */
    protected $_menu;

    public function __construct(array $arguments = array())
    {
        $this->_appConfig = isset($arguments['appConfig']) ? $arguments['appConfig'] : Mage::getConfig();
        $this->_cache = isset($arguments['cache']) ? $arguments['cache'] : Mage::app()->getCacheInstance();
        $this->_acl = isset($arguments['acl'])
            ? $arguments['acl']
            : Mage::getSingleton('Mage_Backend_Model_Auth_Session');
        $this->_urlModel = isset($arguments['urlModel'])
            ? $arguments['urlModel']
            : Mage::getSingleton('Mage_Backend_Model_Url');
        $this->_itemValidator = isset($arguments['itemValidator'])
            ? $arguments['itemValidator']
            : Mage::getSingleton('Mage_Backend_Model_Menu_Item_Validator');
    }

    /**
     * Build menu model from config
     *
     * @return Mage_Backend_Model_Menu
     */
    public function getMenu()
    {
        if (!$this->_menu) {
            $director = $this->_appConfig->getModelInstance(
                'Mage_Backend_Model_Menu_Director_Dom',
                array(
                    'config' => $this->_getDom(),
                    'factory' => $this->_appConfig
                )
            );
            $itemFactory = $this->_appConfig->getModelInstance(
                'Mage_Backend_Model_Menu_Item_Factory',
                array(
                    'acl' => $this->_acl,
                    'objectFactory' => $this->_appConfig,
                    'appConfig' => $this->_appConfig,
                    'storeConfig' => $this->_appConfig->getModelInstance('Mage_Core_Model_Store_Config'),
                    'urlModel' => $this->_urlModel,
                    'validator' => $this->_itemValidator
                )
            );
            $menu = new Mage_Backend_Model_Menu();
            $builder = $this->_appConfig->getModelInstance(
                'Mage_Backend_Model_Menu_Builder',
                array(
                    'menu' => $menu,
                    'itemFactory' => $itemFactory,
                )
            );
            $director->buildMenu($builder);
            $this->_menu = $builder->getResult();
        }
        return $this->_menu;
    }

    /**
     * @return DOMDocument
     */
    protected function _getDom()
    {
        $mergedConfigXml = $this->_loadCache();
        if ($mergedConfigXml) {
            $mergedConfig = new DOMDocument();
            $mergedConfig->loadXML($mergedConfigXml);
        } else {
            $fileList = $this->getMenuConfigurationFiles();
            $mergedConfig = $this->_appConfig
                ->getModelInstance('Mage_Backend_Model_Menu_Config_Menu', $fileList)->getMergedConfig();
            $this->_saveCache($mergedConfig->saveXML());
        }
        return $mergedConfig;
    }

    protected function _loadCache()
    {
        if ($this->_cache->canUse('config')) {
            return $this->_cache->load(self::CACHE_ID);
        }
        return false;
    }

    protected function _saveCache($xml)
    {
        if ($this->_cache->canUse('config')) {
            $this->_cache->save($xml, self::CACHE_ID, array(Mage_Core_Model_Config::CACHE_TAG));
        }
        return $this;
    }

    /**
     * Return array menu configuration files
     *
     * @return array
     */
    public function getMenuConfigurationFiles()
    {
        $files = $this->_appConfig
            ->getModuleConfigurationFiles('adminhtml' . DIRECTORY_SEPARATOR . 'menu.xml');
        return (array) $files;
    }
}
