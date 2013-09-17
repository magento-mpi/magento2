<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend locale model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Backend_Model_Locale extends Magento_Core_Model_Locale
{
    /**
     * @var Magento_Backend_Model_Session
     */
    protected $_session;

    /**
     * @var Magento_Backend_Model_Locale_Manager
     */
    protected $_localeManager;

    /**
     * @var Magento_Core_Controller_Request_Http
     */
    protected $_request;

    /**
     * @var Magento_Core_Model_Locale_Validator
     */
    protected $_localeValidator;

    /**
     * Constructor
     *
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Backend_Model_Session $session
     * @param Magento_Backend_Model_Locale_Manager $localeManager
     * @param Magento_Core_Controller_Request_Http $request
     * @param Magento_Core_Model_Locale_Validator $localeValidator
     * @param string $locale
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Backend_Model_Session $session,
        Magento_Backend_Model_Locale_Manager $localeManager,
        Magento_Core_Controller_Request_Http $request,
        Magento_Core_Model_Locale_Validator $localeValidator,
        $locale = null
    ) {
        $this->_session = $session;
        $this->_localeManager = $localeManager;
        $this->_request = $request;
        $this->_localeValidator = $localeValidator;

        parent::__construct($eventManager, $locale);
    }

    /**
     * Set locale
     *
     * @param   string $locale
     * @return  Magento_Core_Model_LocaleInterface
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
