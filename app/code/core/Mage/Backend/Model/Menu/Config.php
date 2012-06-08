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
     * @var Mage_Core_Model_Event_Manager
     */
    protected $_eventManager;

    /**
     * @var Mage_Backend_Model_Menu_Builder
     */
    protected $_menuBuilder;
    /**
     * Menu model
     *
     * @var Mage_Backend_Model_Menu
     */
    protected $_menu;

    public function __construct(array $arguments = array())
    {
        $this->_cache = isset($arguments['cache']) ? $arguments['cache'] : Mage::app()->getCacheInstance();
        $this->_appConfig = isset($arguments['appConfig']) ? $arguments['appConfig'] : Mage::getConfig();
        $this->_eventManager = isset($arguments['eventManager'])
            ? $arguments['eventManager']
            : Mage::getSingleton('Mage_Core_Model_Event_Manager');

        $this->_menuBuilder = isset($arguments['menuBuilder'])
            ? $arguments['menuBuilder']
            : Mage::getSingleton('Mage_Backend_Model_Menu_Builder', array(
                'menu' => $this->_appConfig->getModelInstance('Mage_Backend_Model_Menu'),
                'itemFactory' => Mage::getSingleton('Mage_Backend_Model_Menu_Item_Factory'),
            ));
    }

    /**
     * Build menu model from config
     *
     * @return Mage_Backend_Model_Menu
     */
    public function getMenu()
    {
        if (!$this->_menu) {
            /* @var $director Mage_Backend_Model_Menu_Director_Dom */
            $director = $this->_appConfig->getModelInstance(
                'Mage_Backend_Model_Menu_Director_Dom',
                array(
                    'config' => $this->_getDom(),
                    'factory' => $this->_appConfig
                )
            );
            $director->buildMenu($this->_menuBuilder);
            $this->_menu = $this->_menuBuilder->getResult();
            $this->_eventManager->dispatch('backend_menu_load_after', array('object' => $this->_menu));
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
