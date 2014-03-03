<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model;

/**
 * Backend locale model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Locale extends \Magento\Core\Model\Locale
{
    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $_session;

    /**
     * @var \Magento\Backend\Model\Locale\Manager
     */
    protected $_localeManager;

    /**
     * @var \Magento\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Core\Model\Locale\Validator
     */
    protected $_localeValidator;

    /**
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\Core\Helper\Translate $translate
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\App\State $appState
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\Locale\Config $config
     * @param \Magento\App\CacheInterface $cache
     * @param \Magento\Stdlib\DateTime $dateTime
     * @param \Magento\Core\Model\Date $dateModel
     * @param \Magento\Backend\Model\Session $session
     * @param \Magento\Backend\Model\Locale\Manager $localeManager
     * @param \Magento\App\RequestInterface $request
     * @param \Magento\Core\Model\Locale\Validator $localeValidator
     * @param mixed $locale
     * 
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Core\Helper\Translate $translate,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\App\State $appState,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\Locale\Config $config,
        \Magento\App\CacheInterface $cache,
        \Magento\Stdlib\DateTime $dateTime,
        \Magento\Core\Model\Date $dateModel,
        \Magento\Backend\Model\Session $session,
        \Magento\Backend\Model\Locale\Manager $localeManager,
        \Magento\App\RequestInterface $request,
        \Magento\Core\Model\Locale\Validator $localeValidator,
        $locale = null
    ) {
        $this->_session = $session;
        $this->_localeManager = $localeManager;
        $this->_request = $request;
        $this->_localeValidator = $localeValidator;
        parent::__construct(
            $eventManager,
            $translate,
            $coreStoreConfig,
            $appState,
            $storeManager,
            $config,
            $cache,
            $dateTime,
            $dateModel,
            $locale
        );
    }

    /**
     * Set locale
     *
     * @param   string $locale
     * @return  $this
     */
    public function setLocale($locale = null)
    {
        parent::setLocale($locale);

        $forceLocale = $this->_request->getParam('locale', null);
        if (!$this->_localeValidator->isValid($forceLocale)) {
            $forceLocale = false;
        }

        $sessionLocale = $this->_session->getSessionLocale();
        $userLocale = $this->_localeManager->getUserInterfaceLocale();

        $localeCodes = array_filter(array($forceLocale, $sessionLocale, $userLocale));

        if (count($localeCodes)) {
            $this->setLocaleCode(reset($localeCodes));
        }

        return $this;
    }
}
