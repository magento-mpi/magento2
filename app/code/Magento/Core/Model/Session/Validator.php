<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Session;

use Magento\Session\Exception;
use Magento\Session\SessionManagerInterface;
use Magento\Session\ValidatorInterface;

/**
 * Session Validator
 */
class Validator implements ValidatorInterface
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
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_storeConfig;

    /**
     * @var \Magento\HTTP\PhpEnvironment\RemoteAddress
     */
    protected $_remoteAddress;

    /**
     * @var array
     */
    protected $_skippedAgentList;

    /**
     * @param \Magento\Core\Model\Store\Config $storeConfig
     * @param \Magento\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
     * @param array $skippedUserAgentList
     */
    public function __construct(
        \Magento\Core\Model\Store\Config $storeConfig,
        \Magento\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        array $skippedUserAgentList = array()
    ) {
        $this->_storeConfig = $storeConfig;
        $this->_remoteAddress = $remoteAddress;
        $this->_skippedAgentList = $skippedUserAgentList;
    }

    /**
     * Validate session
     *
     * @param SessionManagerInterface $session
     * @throws Exception
     * @return void
     */
    public function validate(SessionManagerInterface $session)
    {
        if (!isset($_SESSION[self::VALIDATOR_KEY])) {
            $_SESSION[self::VALIDATOR_KEY] = $this->_getSessionEnvironment();
        } else {
            if (!$this->_validate()) {
                $session->destroy(array('clear_storage' => false));
                // throw core session exception
                throw new Exception('');
            }
        }
    }

    /**
     * Validate data
     *
     * @return bool
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
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
            foreach ($this->_skippedAgentList as $agent) {
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
        if ($this->_remoteAddress->getRemoteAddress()) {
            $parts[self::VALIDATOR_REMOTE_ADDR_KEY] = $this->_remoteAddress->getRemoteAddress();
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
