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
    const CACHE_MENU_OBJECT = 'backend_menu_object';

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
     * @var Mage_Backend_Model_Menu_Factory
     */
    protected $_menuFactory;
    /**
     * Menu model
     *
     * @var Mage_Backend_Model_Menu
     */
    protected $_menu;

    /**
     * @var Mage_Backend_Model_Menu_Logger
     */
    protected $_logger;

    public function __construct(array $arguments = array())
    {
        $this->_cache = isset($arguments['cache']) ? $arguments['cache'] : Mage::app()->getCacheInstance();
        $this->_appConfig = isset($arguments['appConfig']) ? $arguments['appConfig'] : Mage::getConfig();
        $this->_eventManager = isset($arguments['eventManager'])
            ? $arguments['eventManager']
            : Mage::getSingleton('Mage_Core_Model_Event_Manager');

        $this->_logger = isset($arguments['logger'])
            ? $arguments['logger']
            : Mage::getSingleton('Mage_Backend_Model_Menu_Logger');

        $this->_menuFactory = isset($arguments['menuFactory']) ? $arguments['menuFactory'] :
            Mage::getSingleton('Mage_Backend_Model_Menu_Factory');
    }

    /**
     * Build menu model from config
     *
     * @return Mage_Backend_Model_Menu
     * @throws InvalidArgumentException|BadMethodCallException|OutOfRangeException|Exception
     */
    public function getMenu()
    {
        try {
            $this->_initMenu();
            return $this->_menu;
        } catch (InvalidArgumentException $e) {
            $this->_logger->logException($e);
            throw $e;
        } catch (BadMethodCallException $e) {
            $this->_logger->logException($e);
            throw $e;
        } catch (OutOfRangeException $e) {
            $this->_logger->logException($e);
            throw $e;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Initialize menu object
     *
     * @return void
     */
    protected function _initMenu()
    {
        if (!$this->_menu) {
            $this->_menu = $this->_menuFactory->getMenuInstance();

            if ($this->_cache->canUse('config')) {
                $cache = $this->_cache->load(self::CACHE_MENU_OBJECT);
                if ($cache) {
                    $this->_menu->unserialize($cache);
                    return;
                }
            }

            $menuBuilder = $this->_appConfig->getModelInstance('Mage_Backend_Model_Menu_Builder', array(
                'menu' => $this->_menu,
                'itemFactory' => $this->_appConfig->getModelInstance('Mage_Backend_Model_Menu_Item_Factory'),
            ));

            /* @var $director Mage_Backend_Model_Menu_Director_Dom */
            $director = $this->_appConfig->getModelInstance(
                'Mage_Backend_Model_Menu_Director_Dom',
                array(
                    'config' => $this->_getDom(),
                    'factory' => $this->_appConfig,
                    'logger' => $this->_logger
                )
            );
            $director->buildMenu($menuBuilder);
            $this->_eventManager->dispatch('backend_menu_load_after', array('menu' => $this->_menu));

            if ($this->_cache->canUse('config')) {
                $this->_cache->save(
                    $this->_menu->serialize(),
                    self::CACHE_MENU_OBJECT,
                    array(Mage_Core_Model_Config::CACHE_TAG)
                );
            }
        }
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
