<?php
/**
 * Session configuration object
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Session;

/**
 * Magento session configuration
 */
class Config implements \Zend\Session\Config\ConfigInterface
{
    /**
     * Configuration path for cookie domain
     */
    const XML_PATH_COOKIE_DOMAIN    = 'web/cookie/cookie_domain';

    /**
     * Configuration path for cookie lifetime
     */
    const XML_PATH_COOKIE_LIFETIME  = 'web/cookie/cookie_lifetime';

    /**
     * Configuration path for cookie http only param
     */
    const XML_PATH_COOKIE_HTTPONLY  = 'web/cookie/cookie_httponly';

    /**
     * Configuration path for cookie path
     */
    const XML_PATH_COOKIE_PATH      = 'web/cookie/cookie_path';

    /**
     * Cookie default lifetime
     */
    const COOKIE_LIFETIME_DEFAULT = 3600;

    /**
     * All options
     *
     * @var array
     */
    protected $options = array();

    /**
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_storeConfig;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Stdlib\String
     */
    protected $_stringHelper;

    /**
     * @var \Magento\App\RequestInterface
     */
    protected $_httpRequest;

    /**
     * List of boolean options
     *
     * @var array
     */
    protected $booleanOptions = array(
        'session.use_cookies',
        'session.use_only_cookies',
        'session.use_trans_sid',
        'session.cookie_httponly'
    );

    /**
     * @param \Magento\Core\Model\Store\Config $storeConfig
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Stdlib\String $stringHelper
     * @param \Magento\App\RequestInterface $request
     * @param array $options
     */
    public function __construct(
        \Magento\Core\Model\Store\Config $storeConfig,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Stdlib\String $stringHelper,
        \Magento\App\RequestInterface $request,
        array $options = array()
    ) {
        $this->_storeConfig = $storeConfig;
        $this->_storeManager = $storeManager;
        $this->_stringHelper = $stringHelper;
        $this->_httpRequest = $request;

        foreach ($options as $option) {
            $getter = 'get' . $this->_stringHelper->upperCaseWords($option, '_', '');
            if (method_exists($this, $getter)) {
                $value = $this->{$getter}();
            } else {
                $value = $this->getOption($option);
            }
            $this->options[$option] = $value;
        }
    }

    /**
     * Set many options at once
     *
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
     * Get all options set
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set an individual option
     *
     * @param string $option
     * @param mixed $value
     * @return $this
     */
    public function setOption($option, $value)
    {
        $option = $this->getFixedOptionName($option);
        if (!array_key_exists($option, $this->options) || $this->options[$option] != $value) {
            $this->setStorageOption($option, $value);
            $this->options[$option] = $value;
        }

        return $this;
    }

    /**
     * Get an individual option
     *
     * @param string $option
     * @return mixed
     */
    public function getOption($option)
    {
        $option = $this->getFixedOptionName($option);
        if ($this->hasOption($option)) {
            return $this->options[$option];
        }

        $value = $this->getStorageOption($option);
        if (null !== $value) {
            $this->options[$option] = $value;
            return $value;
        }

        return null;
    }

    /**
     * Check to see if an internal option has been set for the key provided.
     *
     * @param string $option
     * @return bool
     */
    public function hasOption($option)
    {
        $option = $this->getFixedOptionName($option);
        return array_key_exists($option, $this->options);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->getOptions();
    }

    /**
     * Set session.name
     *
     * @param string $name
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setName($name)
    {
        $name = (string) $name;
        if (empty($name)) {
            throw new \InvalidArgumentException('Invalid session name; cannot be empty');
        }
        $this->setOption('session.name', $name);
        return $this;
    }

    /**
     * Get session.name
     *
     * @return string
     */
    public function getName()
    {
        return (string) $this->getOption('session.name');
    }

    /**
     * Set session.save_path
     *
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
     * Set session.save_path
     *
     * @return string
     */
    public function getSavePath()
    {
        return (string) $this->getOption('session.save_path');
    }

    /**
     * Set session.cookie_lifetime
     *
     * @param int $cookieLifetime
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setCookieLifetime($cookieLifetime)
    {
        if (!is_numeric($cookieLifetime)) {
            throw new \InvalidArgumentException('Invalid cookie_lifetime; must be numeric');
        }
        if ($cookieLifetime < 0) {
            throw new \InvalidArgumentException(
                'Invalid cookie_lifetime; must be a positive integer or zero'
            );
        }

        $cookieLifetime = (int) $cookieLifetime;
        $this->setOption('session.cookie_lifetime', $cookieLifetime);
        return $this;
    }

    /**
     * Get session.cookie_lifetime
     *
     * @return int
     */
    public function getCookieLifetime()
    {
        if (!$this->hasOption('session.cookie_lifetime')) {
            $lifetime = $this->_storeConfig->getConfig(
                self::XML_PATH_COOKIE_LIFETIME,
                $this->_storeManager->getStore()
            );
            $lifetime = is_numeric($lifetime) ? $lifetime : self::COOKIE_LIFETIME_DEFAULT;
            $this->setCookieLifetime($lifetime);
        }
        return (int) $this->getOption('session.cookie_lifetime');
    }

    /**
     * Set session.cookie_path
     *
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

        $this->setOption('session.cookie_path', $cookiePath);
        return $this;
    }

    /**
     * Get session.cookie_path
     *
     * @return string
     */
    public function getCookiePath()
    {
        if (!$this->hasOption('session.cookie_path')) {
            $path = $this->_storeConfig->getConfig(self::XML_PATH_COOKIE_PATH, $this->_storeManager->getStore());
            if (empty($path)) {
                $path = $this->_httpRequest->getBasePath();
            }
            $this->setCookiePath($path);
        }
        return (string) $this->getOption('session.cookie_path');
    }

    /**
     * Set session.cookie_domain
     *
     * @param string $cookieDomain
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setCookieDomain($cookieDomain)
    {
        if (!is_string($cookieDomain)) {
            throw new \InvalidArgumentException('Invalid cookie domain: must be a string');
        }

        $validator = new \Zend\Validator\Hostname(\Zend\Validator\Hostname::ALLOW_ALL);

        if (!empty($cookieDomain) && !$validator->isValid($cookieDomain)) {
            throw new \InvalidArgumentException(
                'Invalid cookie domain: ' . join('; ', $validator->getMessages())
            );
        }

        $this->setOption('session.cookie_domain', $cookieDomain);
        return $this;
    }

    /**
     * Get session.cookie_domain
     *
     * @return string
     */
    public function getCookieDomain()
    {
        if (!$this->hasOption('session.cookie_domain')) {
            $domain = $this->_storeConfig->getConfig(self::XML_PATH_COOKIE_DOMAIN, $this->_storeManager->getStore());
            $domain = empty($domain) ? $this->_httpRequest->getHttpHost() : $domain;
            $this->setOption('session.cookie_domain', $domain);
        }
        return (string) $this->getOption('session.cookie_domain');
    }

    /**
     * Set session.cookie_secure
     *
     * @param bool $cookieSecure
     * @return $this
     */
    public function setCookieSecure($cookieSecure)
    {
        $this->setOption('session.cookie_secure', (bool) $cookieSecure);
        return $this;
    }

    /**
     * Get session.cookie_secure
     *
     * @return bool
     */
    public function getCookieSecure()
    {
        if (!$this->hasOption('session.cookie_secure')) {
            $this->setCookieSecure($this->_storeManager->getStore()->isAdmin() && $this->_httpRequest->isSecure());
        }
        return (bool) $this->getOption('session.cookie_secure');
    }

    /**
     * Set session.cookie_httponly
     *
     * @param bool $cookieHttpOnly
     * @return $this
     */
    public function setCookieHttpOnly($cookieHttpOnly)
    {
        $this->setOption('session.cookie_httponly', (bool) $cookieHttpOnly);
        return $this;
    }

    /**
     * Get session.cookie_httponly
     *
     * @return bool
     */
    public function getCookieHttpOnly()
    {
        if (!$this->hasOption('session.cookie_httponly')) {
            $this->setCookieHttpOnly(
                $this->_storeConfig->getConfig(self::XML_PATH_COOKIE_HTTPONLY, $this->_storeManager->getStore())
            );
        }
        return (bool) $this->getOption('session.cookie_httponly');
    }

    /**
     * Set session.use_cookies
     *
     * @param bool $useCookies
     * @return $this
     */
    public function setUseCookies($useCookies)
    {
        $this->setOption('session.use_cookies', (bool) $useCookies);
        return $this;
    }

    /**
     * Get session.use_cookies
     *
     * @return bool
     */
    public function getUseCookies()
    {
        return (bool) $this->getOption('session.use_cookies');
    }

    /**
     * Set remember_me_seconds
     *
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

        $this->options['remember_me_seconds'] = $rememberMeSeconds;
        return $this;
    }

    /**
     * Get remember_me_seconds
     *
     * @return int
     */
    public function getRememberMeSeconds()
    {
        return (int) isset($this->options['remember_me_seconds']) ? $this->options['remember_me_seconds'] : 1209600;
    }

    /**
     * Set storage option in backend configuration store
     *
     * @param string $option
     * @param mixed $value
     * @return $this
     * @throws \InvalidArgumentException
     */
    protected function setStorageOption($option, $value)
    {
        $result = ini_set($option, $value);
        if ($result === false) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid sessions-related ini setting.', $option));
        }

        return $this;
    }

    /**
     * Retrieve a storage option from a backend configuration store
     *
     * @param string $option
     * @return mixed
     */
    protected function getStorageOption($option)
    {
        $value = ini_get($option);
        if (in_array($option, $this->booleanOptions)) {
            $value = (bool) $value;
        }

        return $value;
    }

    /**
     * Fix session option name
     *
     * @param string $option
     * @return string
     */
    protected function getFixedOptionName($option)
    {
        $option = strtolower($option);

        switch ($option) {
            case 'remember_me_seconds':
                // do nothing; not an INI option
                return;
            case 'url_rewriter_tags':
                $option = 'url_rewriter.tags';
                break;
            default:
                if (strpos($option, 'session.') !== 0) {
                    $option = 'session.' . $option;
                }
                break;
        }

        return $option;
    }

    /**
     * Intercept get*() and set*() methods
     *
     * Intercepts getters and setters and passes them to getOption() and setOption(),
     * respectively.
     *
     * @param  string $method
     * @param  array $args
     * @return mixed
     * @throws \BadMethodCallException on non-getter/setter method
     */
    public function __call($method, $args)
    {
        $prefix = substr($method, 0, 3);
        $option = substr($method, 3);
        $key    = strtolower(preg_replace('#(?<=[a-z])([A-Z])#', '_\1', $option));

        if ($prefix === 'set') {
            $value  = array_shift($args);
            return $this->setOption($key, $value);
        } elseif ($prefix === 'get') {
            return $this->getOption($key);
        } else {
            throw new \BadMethodCallException(sprintf(
                'Method "%s" does not exist in %s',
                $method,
                get_class($this)
            ));
        }
    }
}
