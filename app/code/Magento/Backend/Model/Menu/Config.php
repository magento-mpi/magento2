<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Menu;

class Config
{
    const CACHE_ID = 'backend_menu_config';
    const CACHE_MENU_OBJECT = 'backend_menu_object';

    /**
     * @var \Magento\Core\Model\Cache\Type\Config
     */
    protected $_configCacheType;

    /**
     * @var \Magento\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * @var \Magento\Backend\Model\MenuFactory
     */
    protected $_menuFactory;
    /**
     * Menu model
     *
     * @var \Magento\Backend\Model\Menu
     */
    protected $_menu;

    /**
     * @var \Magento\Logger
     */
    protected $_logger;

    /**
     * @var \Magento\Backend\Model\Menu\Config\Reader
     */
    protected $_configReader;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Backend\Model\Menu\AbstractDirector
     */
    protected $_director;

    /**
     * @param \Magento\Backend\Model\Menu\Builder $menuBuilder
     * @param \Magento\Backend\Model\Menu\AbstractDirector $menuDirector
     * @param \Magento\Backend\Model\MenuFactory $menuFactory
     * @param \Magento\Backend\Model\Menu\Config\Reader $configReader
     * @param \Magento\Core\Model\Cache\Type\Config $configCacheType
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\Logger $logger
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Backend\Model\Menu\Builder $menuBuilder,
        \Magento\Backend\Model\Menu\AbstractDirector $menuDirector,
        \Magento\Backend\Model\MenuFactory $menuFactory,
        \Magento\Backend\Model\Menu\Config\Reader $configReader,
        \Magento\Core\Model\Cache\Type\Config $configCacheType,
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Logger $logger,
        \Magento\Core\Model\StoreManagerInterface $storeManager
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
     * @return \Magento\Backend\Model\Menu
     * @throws \Exception|\InvalidArgumentException
     * @throws \Exception
     * @throws \BadMethodCallException|\Exception
     * @throws \Exception|\OutOfRangeException
     */
    public function getMenu()
    {
        if ($this->_storeManager->getStore()->getConfig('dev/log/active')) {
            $this->_logger->addStreamLog(\Magento\Backend\Model\Menu::LOGGER_KEY);
        }

        try {
            $this->_initMenu();
            return $this->_menu;
        } catch (\InvalidArgumentException $e) {
            $this->_logger->logException($e);
            throw $e;
        } catch (\BadMethodCallException $e) {
            $this->_logger->logException($e);
            throw $e;
        } catch (\OutOfRangeException $e) {
            $this->_logger->logException($e);
            throw $e;
        } catch (\Exception $e) {
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
                $this->_configReader->read(\Magento\Core\Model\App\Area::AREA_ADMINHTML),
                $this->_menuBuilder,
                $this->_logger
            );
            $this->_menu = $this->_menuBuilder->getResult($this->_menu);

            $this->_configCacheType->save($this->_menu->serialize(), self::CACHE_MENU_OBJECT);
        }
    }
}
