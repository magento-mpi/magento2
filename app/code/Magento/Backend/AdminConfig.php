<?php
/**
 * Backend Session configuration object
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\Session\Config\ConfigInterface;
use Magento\Framework\Session\Config;

/**
 * Magento Backend session configuration
 *
 * @method Config setSaveHandler()
 */
class AdminConfig implements ConfigInterface
{
    /**
     * @var FrontNameResolver $frontNameResolver
     */
    protected $frontNameResolver;

    /**
     * @var Config $config
     */
    protected $config;
    /**
     * @param FrontNameResolver $frontNameResolver
     * @param Config $config
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        FrontNameResolver $frontNameResolver,
        Config $config,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->frontNameResolver = $frontNameResolver;
        $this->config = $config;

        $baseUrl = $request->getBaseUrl();
        $adminPath = $this->extractAdminPath($baseUrl);
        $this->config->setCookiePath($adminPath);
    }

    /**
     * Determine the admin path
     *
     * @param string $baseUrl
     * @return string
     * @throws \InvalidArgumentException
     */
    private function extractAdminPath($baseUrl)
    {
        if (!is_string($baseUrl)) {
            throw new \InvalidArgumentException('Cookie path is not a string.');
        }

        $adminPath = $this->frontNameResolver->getFrontName();

        if (!substr($baseUrl, -1) || ('/' != substr($baseUrl, -1))) {
            $baseUrl = $baseUrl . '/';
        }

        return $baseUrl . $adminPath;
    }

    /**
     * Wrapper function for setOptions
     *
     * @param array $options
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setOptions($options)
    {
        return $this->config->setOptions($options);
    }

    /**
     * Wrapper function for getOptions
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->config->getOptions();
    }

    /**
     * Wrapper function for setOption
     *
     * @param string $option
     * @param mixed $value
     * @return $this
     */
    public function setOption($option, $value)
    {
        return $this->config->setOption($option, $value);
    }

    /**
     * Wrapper function for getOption
     *
     * @param string $option
     * @return mixed
     */
    public function getOption($option)
    {
        return $this->config->getOption($option);
    }

    /**
     * Wrapper function for hasOption
     *
     * @param string $option
     * @return bool
     */
    public function hasOption($option)
    {
        return $this->config->hasOption($option);
    }

    /**
     * Wrapper function for toArray
     *
     * @return array
     */
    public function toArray()
    {
        return $this->config->toArray();
    }

    /**
     * Wrapper function for setName
     *
     * @param string $name
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setName($name)
    {
        return $this->config->setName($name);
    }

    /**
     * Wrapper function for getName
     *
     * @return string
     */
    public function getName()
    {
        return $this->config->getName();
    }

    /**
     * Wrapper function for setSavePath
     *
     * @param string $savePath
     * @return $this
     */
    public function setSavePath($savePath)
    {
        return $this->config->setSavePath($savePath);
    }

    /**
     * Wrapper function for getSavePath
     *
     * @return string
     */
    public function getSavePath()
    {
        return $this->config->getSavePath();
    }

    /**
     * Wrapper function for setCookieLifetime
     *
     * @param int $cookieLifetime
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setCookieLifetime($cookieLifetime)
    {
        return $this->config->setCookieLifetime($cookieLifetime);
    }

    /**
     * Wrapper function for getCookieLifetime
     *
     * @return int
     */
    public function getCookieLifetime()
    {
        return $this->config->getCookieLifetime();
    }

    /**
     * Wrapper function for setCookiePath
     *
     * @param string $cookiePath
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setCookiePath($cookiePath)
    {
        return $this->config->setCookiePath($cookiePath);
    }

    /**
     * Wrapper function for getCookiePath
     *
     * @return string
     */
    public function getCookiePath()
    {
        return $this->config->getCookiePath();
    }

    /**
     * Wrapper function for setCookieDomain
     *
     * @param string $cookieDomain
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setCookieDomain($cookieDomain)
    {
        return $this->config->setCookieDomain($cookieDomain);
    }

    /**
     * Wrapper function for getCookieDomain
     *
     * @return string
     */
    public function getCookieDomain()
    {
        return $this->config->getCookieDomain();
    }

    /**
     * Wrapper function for setCookieSecure
     *
     * @param bool $cookieSecure
     * @return $this
     */
    public function setCookieSecure($cookieSecure)
    {
        return $this->config->setCookieSecure($cookieSecure);
    }

    /**
     * Wrapper function for getCookieSecure
     *
     * @return bool
     */
    public function getCookieSecure()
    {
        return $this->config->getCookieSecure();
    }

    /**
     * Wrapper function for setCookieHttpOnly
     *
     * @param bool $cookieHttpOnly
     * @return $this
     */
    public function setCookieHttpOnly($cookieHttpOnly)
    {
        return $this->config->setCookieHttpOnly($cookieHttpOnly);
    }

    /**
     * Wrapper function for getCookieHttpOnly
     *
     * @return bool
     */
    public function getCookieHttpOnly()
    {
        return $this->config->getCookieHttpOnly();
    }

    /**
     * Wrapper function for setUseCookies
     *
     * @param bool $useCookies
     * @return $this
     */
    public function setUseCookies($useCookies)
    {
        return $this->config->setUseCookies($useCookies);
    }

    /**
     * Wrapper function for getUseCookies
     *
     * @return bool
     */
    public function getUseCookies()
    {
        return $this->config->getUseCookies();
    }

    /**
     * Wrapper function for setStorageOption
     *
     * @param string $option
     * @param string $value
     * @return $this
     * @throws \InvalidArgumentException
     */
    protected function setStorageOption($option, $value)
    {
        return $this->config->setStorageOption($option, $value);
    }

    /**
     * Wrapper function for getStorageOption
     *
     * @param string $option
     * @return string|bool
     */
    protected function getStorageOption($option)
    {
        return $this->config->getStorageOption($option);
    }

    /**
     * Wrapper function for getFixedOptionName
     *
     * @param string $option
     * @return string
     */
    protected function getFixedOptionName($option)
    {
        return $this->config->getFixedOptionName($option);
    }

    /**
     * Wrapper function for __call
     *
     * Intercepts getters and setters and passes them to getOption() and setOption(),
     * respectively.
     *
     * @param  string $method
     * @param  array $args
     * @return mixed
     * @throws \BadMethodCallException On non-getter/setter method
     */
    public function __call($method, $args)
    {
        return $this->config->__call($method, $args);
    }
}
