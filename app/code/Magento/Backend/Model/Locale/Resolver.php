<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Locale;

/**
 * Backend locale model
 */
class Resolver extends \Magento\Locale\Resolver
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
     * @var \Magento\Locale\Validator
     */
    protected $_localeValidator;

    /**
     * @param \Magento\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\AppInterface $app
     * @param \Magento\LocaleFactory $localeFactory
     * @param string $defaultLocalePath
     * @param string $scopeType
     * @param \Magento\Backend\Model\Session $session
     * @param Manager $localeManager
     * @param \Magento\App\RequestInterface $request
     * @param \Magento\Locale\Validator $localeValidator
     * @param string|null $locale
     */
    public function __construct(
        \Magento\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\AppInterface $app,
        \Magento\LocaleFactory $localeFactory,
        $defaultLocalePath,
        $scopeType,
        \Magento\Backend\Model\Session $session,
        \Magento\Backend\Model\Locale\Manager $localeManager,
        \Magento\App\RequestInterface $request,
        \Magento\Locale\Validator $localeValidator,
        $locale = null
    ) {
        $this->_session = $session;
        $this->_localeManager = $localeManager;
        $this->_request = $request;
        $this->_localeValidator = $localeValidator;
        parent::__construct($scopeConfig, $app, $localeFactory, $defaultLocalePath, $scopeType, $locale);
    }

    /**
     * Set locale
     *
     * @param string $locale
     * @return $this
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
