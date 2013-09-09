<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Backend_Model_Menu_Config
{
    const CACHE_ID = 'backend_menu_config';
    const CACHE_MENU_OBJECT = 'backend_menu_object';

    /**
     * @var Magento_Core_Model_Cache_Type_Config
     */
    protected $_configCacheType;

    /**
     * @var Magento_Core_Model_Event_Manager
     */
    protected $_eventManager;

    /**
     * @var Magento_Backend_Model_MenuFactory
     */
    protected $_menuFactory;
    /**
     * Menu model
     *
     * @var Magento_Backend_Model_Menu
     */
    protected $_menu;

    /**
     * @var Magento_Core_Model_Logger
     */
    protected $_logger;

    /**
     * @var Magento_Backend_Model_Menu_Config_Reader
     */
    protected $_configReader;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Magento_Backend_Model_Menu_DirectorAbstract
     */
    protected $_director;

    /**
     * @param Magento_Backend_Model_Menu_Builder $menuBuilder
     * @param Magento_Backend_Model_Menu_DirectorAbstract $menuDirector
     * @param Magento_Backend_Model_MenuFactory $menuFactory
     * @param Magento_Backend_Model_Menu_Config_Reader $configReader
     * @param Magento_Core_Model_Cache_Type_Config $configCacheType
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     */
    public function __construct(
        Magento_Backend_Model_Menu_Builder $menuBuilder,
        Magento_Backend_Model_Menu_DirectorAbstract $menuDirector,
        Magento_Backend_Model_MenuFactory $menuFactory,
        Magento_Backend_Model_Menu_Config_Reader $configReader,
        Magento_Core_Model_Cache_Type_Config $configCacheType,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Logger $logger,
        Magento_Core_Model_StoreManagerInterface $storeManager
    ) {
        $this->_menuBuilder = $menuBuilder;
        $this->_director = $menuDirector;
        $this->_configCacheType = $configCacheType;
        $this->_eventManager = $eventManager;
        $this->_logger = $logger;
        $this->_menuFactory = $menuFactory;
        $this->_configReader = $configReader;
        $this->_storeManager = $storeManager;
    }

    /**
     * Build menu model from config
     *
     * @return Magento_Backend_Model_Menu
     * @throws InvalidArgumentException|BadMethodCallException|OutOfRangeException|Exception
     */
    public function getMenu()
    {
        $store = $this->_storeManager->getStore();
        $this->_logger->addStoreLog(Magento_Backend_Model_Menu::LOGGER_KEY, $store);
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
            $this->_menu = $this->_menuFactory->create();

            $cache = $this->_configCacheType->load(self::CACHE_MENU_OBJECT);
            if ($cache) {
                $this->_menu->unserialize($cache);
                return;
            }

            $this->_director->direct(
                $this->_configReader->read(Magento_Core_Model_App_Area::AREA_ADMINHTML),
                $this->_menuBuilder,
                $this->_logger
            );
            $this->_menu = $this->_menuBuilder->getResult($this->_menu);

            $this->_configCacheType->save($this->_menu->serialize(), self::CACHE_MENU_OBJECT);
        }
    }
}
