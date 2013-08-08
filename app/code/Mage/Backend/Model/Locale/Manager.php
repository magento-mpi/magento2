<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Locale manager model
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Model_Locale_Manager
{
    /**
     * @var Mage_Backend_Model_Session
     */
    protected $_session;

    /**
     * @var Mage_Backend_Model_Auth_Session
     */
    protected $_authSession;

    /**
     * @var Magento_Core_Model_Translate
     */
    protected $_translator;

    /**
     * Constructor
     *
     * @param Mage_Backend_Model_Session $session
     * @param Mage_Backend_Model_Auth_Session $authSession
     * @param Magento_Core_Model_Translate $translator
     */
    public function __construct(
        Mage_Backend_Model_Session $session,
        Mage_Backend_Model_Auth_Session $authSession,
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
     * @return Mage_Backend_Model_Locale_Manager
     */
    public function switchBackendInterfaceLocale($localeCode)
    {
        $this->_session->setSessionLocale(null);

        $this->_authSession->getUser()
            ->setInterfaceLocale($localeCode);

        $this->_translator->setLocale($localeCode)
            ->init(Mage_Backend_Helper_Data::BACKEND_AREA_CODE, true);

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
