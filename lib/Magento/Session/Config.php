<?php
/**
 * Session configuration object
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Session;

use Zend\Validator;

class Config implements ConfigInterface
{
    /**
     * @var array
     */
    protected $_data;

    /**
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_storeConfig;

    /**
     * @var \Magento\Core\Model\StoreManager
     */
    protected $_storeManager;

    /**
     * @var \Magento\Stdlib\String
     */
    protected $_stringHelper;

    /**
     * @param \Magento\Core\Model\Store\Config $storeConfig
     * @param \Magento\Core\Model\StoreManager $storeManager
     * @param \Magento\Stdlib\String $stringHelper
     */
    public function __construct(
        \Magento\Core\Model\Store\Config $storeConfig,
        \Magento\Core\Model\StoreManager $storeManager,
        \Magento\Stdlib\String $stringHelper
    ) {
        /**
            TODO: $this->_data should contain all values, otherwise toArray() and getOptions() will not return
            full set of options

        */


        $this->_storeConfig = $storeConfig;
        $this->_storeManager = $storeManager;
        $this->_stringHelper = $stringHelper;
    }

    /**
     * @param array $options
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setOptions($options)
    {
        if (!is_array($options) && !$options instanceof \Traversable) {
            throw new \InvalidArgumentException(sprintf(
                'Parameter provided to %s must be an array or Traversable',
                __METHOD__
            ));
        }

        foreach ($options as $option => $value) {
            $setter = 'set' . $this->_stringHelper->upperCaseWords($option, '_', '');
            if (method_exists($this, $setter)) {
                $this->{$setter}($value);
            } else {
                $this->setOption($option, $value);
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->_data;
    }

    /**
     * @param string $option
     * @param mixed $value
     * @return $this
     */
    public function setOption($option, $value)
    {
        //TODO: validate that option exists and is related to session

        $option = $this->_getFixedOptionName($option);
        if (!array_key_exists($option, $this->_data) || $this->_data[$option] != $value) {
            $this->_setStorageOption($option, $value);
            $this->_data[$option] = $value;
        }

        return $this;
    }

    /**
     * @param string $option
     * @return mixed
     */
    public function getOption($option)
    {
        $option = $this->_getFixedOptionName($option);
        if ($this->hasOption($option)) {
            return $this->_data[$option];
        }

        $value = $this->_getStorageOption($option);
        if ($value !== null) {
            $this->_data[$option] = $value;
            return $value;
        }

        return null;
    }

    /**
     * @param string $option
     * @return bool
     */
    public function hasOption($option)
    {
        $option = $this->_getFixedOptionName($option);
        return array_key_exists($option, $this->_data);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->getOptions();
    }

    /**
     * @param string $name
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setName($name)
    {
        $name = (string)$name;
        if (empty($name)) {
            throw new \InvalidArgumentException('Invalid session name; cannot be empty');
        }
        $this->setOption('session.name', $name);
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return (string)$this->getOption('session.name');
    }

    /**
     * @param string $savePath
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setSavePath($savePath)
    {
        if (!is_dir($savePath)) {
            throw new \InvalidArgumentException('Invalid save_path provided; not a directory');
        }
        if (!is_writable($savePath)) {
            throw new \InvalidArgumentException('Invalid save_path provided; not writable');
        }

        $this->setOption('session.save_path', $savePath);
        return $this;
    }

    /**
     * @return string
     */
    public function getSavePath()
    {
        return (string)$this->getOption('session.save_path');
    }

    /**
     * @param int $cookieLifetime
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setCookieLifetime($cookieLifetime)
    {
        if (!is_numeric($cookieLifetime)) {
            throw new \InvalidArgumentException('Invalid cookie_lifetime; must be numeric');
        }
        if (0 > $cookieLifetime) {
            throw new \InvalidArgumentException(
                'Invalid cookie_lifetime; must be a positive integer or zero'
            );
        }

        $cookieLifetime = (int) $cookieLifetime;
        $this->setOption('session.cookie_lifetime', $cookieLifetime);
        return $this;
    }

    /**
     * @return int
     */
    public function getCookieLifetime()
    {
        return (int)$this->getOption('session.cookie_lifetime');
    }

    /**
     * @param string $cookiePath
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setCookiePath($cookiePath)
    {
        $cookiePath = (string) $cookiePath;

        $test = parse_url($cookiePath, PHP_URL_PATH);
        if ($test != $cookiePath || '/' != $test[0]) {
            throw new \InvalidArgumentException('Invalid cookie path');
        }

        $this->cookiePath = $cookiePath;
        $this->setOption('session.cookie_path', $cookiePath);
        return $this;
    }

    /**
     * @return string
     */
    public function getCookiePath()
    {
        return (string)$this->getOption('session.cookie_path');
    }

    /**
     * @param string $cookieDomain
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setCookieDomain($cookieDomain)
    {
        if (!is_string($cookieDomain)) {
            throw new \InvalidArgumentException('Invalid cookie domain: must be a string');
        }

        $validator = new Validator\Hostname(Validator\Hostname::ALLOW_ALL);

        if (!empty($cookieDomain) && !$validator->isValid($cookieDomain)) {
            throw new \InvalidArgumentException(
                'Invalid cookie domain: ' . join('; ', $validator->getMessages())
            );
        }

        $this->setOption('session.cookie_domain', $cookieDomain);
        return $this;
    }

    /**
     * @return string
     */
    public function getCookieDomain()
    {
        return (string)$this->getOption('session.cookie_domain');
    }

    /**
     * @param bool $cookieSecure
     * @return $this
     */
    public function setCookieSecure($cookieSecure)
    {
        $this->setOption('session.cookie_secure', (bool)$cookieSecure);
        return $this;
    }

    /**
     * @return bool
     */
    public function getCookieSecure()
    {
        return (bool)$this->getOption('session.cookie_secure');
    }

    /**
     * @param bool $cookieHttpOnly
     * @return $this
     */
    public function setCookieHttpOnly($cookieHttpOnly)
    {
        $this->setOption('session.cookie_httponly', (bool)$cookieHttpOnly);
        return $this;
    }

    /**
     * @return bool
     */
    public function getCookieHttpOnly()
    {
        return (bool)$this->getOption('session.cookie_httponly');
    }

    /**
     * @param bool $useCookies
     * @return $this
     */
    public function setUseCookies($useCookies)
    {
        $this->setOption('session.use_cookies', (bool)$useCookies);
        return $this;
    }

    /**
     * @return bool
     */
    public function getUseCookies()
    {
        return (bool)$this->getOption('session.use_cookies');
    }

    /**
     * @param int $rememberMeSeconds
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setRememberMeSeconds($rememberMeSeconds)
    {
        if (!is_numeric($rememberMeSeconds)) {
            throw new \InvalidArgumentException('Invalid remember_me_seconds; must be numeric');
        }

        $rememberMeSeconds = (int) $rememberMeSeconds;
        if ($rememberMeSeconds < 1) {
            throw new \InvalidArgumentException('Invalid remember_me_seconds; must be a positive integer');
        }

        $this->_data['remember_me_seconds'] = $rememberMeSeconds;
        return $this;
    }

    /**
     * @return int
     */
    public function getRememberMeSeconds()
    {
        return (int)$this->_data['remember_me_seconds'];
    }

    /**
     * @param string $option
     * @return mixed
     */
    protected function _getStorageOption($option)
    {
        $booleanOptions = array(
            'session.use_cookies',
            'session.use_only_cookies',
            'session.use_trans_sid',
            'session.cookie_httponly'
        );

        $value = ini_get($option);
        if (in_array($option, $booleanOptions)) {
            $value = (bool) $value;
        }

        return $value;
    }

    /**
     * @param string $option
     * @param mixed $value
     * @return $this
     * @throws \InvalidArgumentException
     */
    protected function _setStorageOption($option, $value)
    {
        $result = ini_set($option, $value);
        if ($result === false) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid sessions-related ini setting.', $option));
        }

        return $this;
    }

    /**
     * @param string $option
     * @return string
     */
    protected function _getFixedOptionName($option)
    {
        $option = strtolower($option);

        switch ($option) {
            case 'url_rewriter_tags':   //TODO
                $option = 'url_rewriter.tags';
                break;

            default:
                if (strpos('session.', $option) !== 0) {
                    $option = 'session.' . $option;
                }
                break;
        }

        return $option;
    }
}
