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
 * Locale manager model
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Backend_Model_Locale_Manager
{
    /**
     * @var Magento_Backend_Model_Session
     */
    protected $_session;

    /**
     * @var Magento_Backend_Model_Auth_Session
     */
    protected $_authSession;

    /**
     * @var Magento_Core_Model_Translate
     */
    protected $_translator;

    /**
     * Constructor
     *
     * @param Magento_Backend_Model_Session $session
     * @param Magento_Backend_Model_Auth_Session $authSession
     * @param Magento_Core_Model_Translate $translator
     */
    public function __construct(
        Magento_Backend_Model_Session $session,
        Magento_Backend_Model_Auth_Session $authSession,
        Magento_Core_Model_Translate $translator
    ) {
        $this->_session = $session;
        $this->_authSession = $authSession;
        $this->_translator = $translator;
    }

    /**
     * Switch backend locale according to locale code
     *
     * @param string $localeCode
     * @return Magento_Backend_Model_Locale_Manager
     */
    public function switchBackendInterfaceLocale($localeCode)
    {
        $this->_session->setSessionLocale(null);

        $this->_authSession->getUser()
            ->setInterfaceLocale($localeCode);

        $this->_translator->setLocale($localeCode)
            ->init(Magento_Backend_Helper_Data::BACKEND_AREA_CODE, true);

        return $this;
    }

    /**
     * Get user interface locale stored in session data
     *
     * @return string
     */
    public function getUserInterfaceLocale()
    {
        $interfaceLocale = Magento_Core_Model_LocaleInterface::DEFAULT_LOCALE;

        $userData = $this->_authSession->getUser();
        if ($userData && $userData->getInterfaceLocale()) {
            $interfaceLocale = $userData->getInterfaceLocale();
        }

        return $interfaceLocale;
    }
}
