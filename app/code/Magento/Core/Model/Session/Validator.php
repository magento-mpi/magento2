<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Session_Validator
{
    const VALIDATOR_KEY                         = '_session_validator_data';
    const VALIDATOR_HTTP_USER_AGENT_KEY         = 'http_user_agent';
    const VALIDATOR_HTTP_X_FORWARDED_FOR_KEY    = 'http_x_forwarded_for';
    const VALIDATOR_HTTP_VIA_KEY                = 'http_via';
    const VALIDATOR_REMOTE_ADDR_KEY             = 'remote_addr';

    const XML_PATH_USE_REMOTE_ADDR      = 'web/session/use_remote_addr';
    const XML_PATH_USE_HTTP_VIA         = 'web/session/use_http_via';
    const XML_PATH_USE_X_FORWARDED      = 'web/session/use_http_x_forwarded_for';
    const XML_PATH_USE_USER_AGENT       = 'web/session/use_http_user_agent';

    /**
     * @var Magento_Core_Model_Store_Config
     */
    protected $_storeConfig;

    /**
     * @var Magento_Core_Helper_Http
     */
    protected $_helper;

    /**
     * @var array
     */
    protected $_skippedUserAgentList;

    /**
     * @param Magento_Core_Model_Store_Config $storeConfig
     * @param Magento_Core_Helper_Http $helper
     * @param array $skippedUserAgentList
     */
    public function __construct(
        Magento_Core_Model_Store_Config $storeConfig,
        Magento_Core_Helper_Http $helper,
        array $skippedUserAgentList = array()
    ) {
        $this->_storeConfig = $storeConfig;
        $this->_helper = $helper;
        $this->_skippedUserAgentList = $skippedUserAgentList;
    }

    /**
     * Validate session
     *
     * @param Magento_Core_Model_Session_Abstract $session
     * @throws Magento_Core_Model_Session_Exception
     */
    public function validate(Magento_Core_Model_Session_Abstract $session)
    {
        if (!isset($_SESSION[self::VALIDATOR_KEY])) {
            $_SESSION[self::VALIDATOR_KEY] = $this->_getSessionEnvironment();
        } else {
            if (!$this->_validate()) {
                $session->getCookie()->delete(session_name());
                // throw core session exception
                throw new Magento_Core_Model_Session_Exception('');
            }
        }
    }

    /**
     * Validate data
     *
     * @return bool
     */
    protected function _validate()
    {
        $sessionData = $_SESSION[self::VALIDATOR_KEY];
        $validatorData = $this->_getSessionEnvironment();

        if ($this->_storeConfig->getConfig(self::XML_PATH_USE_REMOTE_ADDR)
            && $sessionData[self::VALIDATOR_REMOTE_ADDR_KEY] != $validatorData[self::VALIDATOR_REMOTE_ADDR_KEY]
        ) {
            return false;
        }
        if ($this->_storeConfig->getConfig(self::XML_PATH_USE_HTTP_VIA)
            && $sessionData[self::VALIDATOR_HTTP_VIA_KEY] != $validatorData[self::VALIDATOR_HTTP_VIA_KEY]
        ) {
            return false;
        }

        $httpXForwardedKey = $sessionData[self::VALIDATOR_HTTP_X_FORWARDED_FOR_KEY];
        $validatorXForwarded = $validatorData[self::VALIDATOR_HTTP_X_FORWARDED_FOR_KEY];
        if ($this->_storeConfig->getConfig(self::XML_PATH_USE_X_FORWARDED)
            && $httpXForwardedKey != $validatorXForwarded ) {
            return false;
        }
        if ($this->_storeConfig->getConfig(self::XML_PATH_USE_USER_AGENT)
            && $sessionData[self::VALIDATOR_HTTP_USER_AGENT_KEY] != $validatorData[self::VALIDATOR_HTTP_USER_AGENT_KEY]
        ) {
            foreach ($this->_skippedUserAgentList as $agent) {
                if (preg_match('/' . $agent . '/iu', $validatorData[self::VALIDATOR_HTTP_USER_AGENT_KEY])) {
                    return true;
                }
            }
            return false;
        }

        return true;
    }

    /**
     * Prepare session environment data for validation
     *
     * @return array
     */
    protected function _getSessionEnvironment()
    {
        $parts = array(
            self::VALIDATOR_REMOTE_ADDR_KEY             => '',
            self::VALIDATOR_HTTP_VIA_KEY                => '',
            self::VALIDATOR_HTTP_X_FORWARDED_FOR_KEY    => '',
            self::VALIDATOR_HTTP_USER_AGENT_KEY         => ''
        );

        // collect ip data
        if ($this->_helper->getRemoteAddr()) {
            $parts[self::VALIDATOR_REMOTE_ADDR_KEY] = $this->_helper->getRemoteAddr();
        }
        if (isset($_ENV['HTTP_VIA'])) {
            $parts[self::VALIDATOR_HTTP_VIA_KEY] = (string)$_ENV['HTTP_VIA'];
        }
        if (isset($_ENV['HTTP_X_FORWARDED_FOR'])) {
            $parts[self::VALIDATOR_HTTP_X_FORWARDED_FOR_KEY] = (string)$_ENV['HTTP_X_FORWARDED_FOR'];
        }

        // collect user agent data
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $parts[self::VALIDATOR_HTTP_USER_AGENT_KEY] = (string)$_SERVER['HTTP_USER_AGENT'];
        }

        return $parts;
    }
}
