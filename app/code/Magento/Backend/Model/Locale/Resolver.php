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
     * @var \Magento\Core\Model\Locale\Validator
     */
    protected $_localeValidator;

    /**
     * @param \Magento\Core\Model\Store\ConfigInterface $coreStoreConfig
     * @param \Magento\AppInterface $app
     * @param \Magento\Core\Helper\Translate $translate
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\Backend\Model\Session $session
     * @param Manager $localeManager
     * @param \Magento\App\RequestInterface $request
     * @param \Magento\Core\Model\Locale\Validator $localeValidator
     * @param string|null $locale
     */
    public function __construct(
        \Magento\Core\Model\Store\ConfigInterface $coreStoreConfig,
        \Magento\AppInterface $app,
        \Magento\Core\Helper\Translate $translate,
        \Magento\ObjectManager $objectManager,
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
        parent::__construct($coreStoreConfig, $app, $translate, $objectManager, $locale);
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
