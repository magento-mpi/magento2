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
     * @var Magento_ObjectManager
     */
    protected $_factory;

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

    /**
     * @var Mage_Backend_Model_Menu_Logger
     */
    protected $_logger;

    /**
     * @param Mage_Core_Model_Cache $cache
     * @param Magento_ObjectManager $factory
     * @param Mage_Core_Model_Config $config
     * @param Mage_Core_Model_Event_Manager $eventManager
     * @param Mage_Backend_Model_Menu_Logger $menuLogger
     * @param Mage_Backend_Model_Menu_Builder $menuBuilder
     */
    public function __construct(
        Mage_Core_Model_Cache $cache,
        Magento_ObjectManager $factory,
        Mage_Core_Model_Config $config,
        Mage_Core_Model_Event_Manager $eventManager,
        Mage_Backend_Model_Menu_Logger $menuLogger,
        Mage_Backend_Model_Menu_Builder $menuBuilder
    ) {
        $this->_cache = $cache;
        $this->_factory = $factory;
        $this->_appConfig = $config;
        $this->_eventManager = $eventManager;
        $this->_logger = $menuLogger;
        $this->_menuBuilder = $menuBuilder;
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
            /* @var $director Mage_Backend_Model_Menu_Director_Dom */
            $director = $this->_factory->create(
                'Mage_Backend_Model_Menu_Director_Dom',
                array(
                    'menuConfig' => $this->_getDom(),
                    'factory' => $this->_factory,
                    'menuLogger' => $this->_logger
                )
            );
            $director->buildMenu($this->_menuBuilder);
            $this->_menu = $this->_menuBuilder->getResult();
            $this->_eventManager->dispatch('backend_menu_load_after', array('menu' => $this->_menu));
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
            $mergedConfig = $this->_factory
                ->create('Mage_Backend_Model_Menu_Config_Menu', array('configFiles' => $fileList))
                ->getMergedConfig();
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
