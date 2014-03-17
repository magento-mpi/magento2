<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\App\Area;

/**
 * Class CachePlugin
 * Should add design exceptions o identifier for built-in cache
 *
 * @package Magento\Core\Model\App\Area
 */
class CacheIdentifierPlugin
{
    /**
     * Constructor
     *
     * @param DesignExceptions                $designExceptions
     * @param \Magento\App\RequestInterface   $request
     * @param \Magento\PageCache\Model\Config $config
     */
    public function __construct(
        \Magento\Core\Model\App\Area\DesignExceptions $designExceptions,
        \Magento\App\RequestInterface $request,
        \Magento\PageCache\Model\Config $config
    ) {
        $this->designExceptions = $designExceptions;
        $this->request = $request;
        $this->config = $config;
    }

    /**
     * Adds a theme key to identifier for a built-in cache if user-agent theme rule is actual
     *
     * @param \Magento\App\PageCache\Identifier $identifier
     * @param string $result
     * @return string
     */
    public function afterGetValue(\Magento\App\PageCache\Identifier $identifier, $result)
    {
        if ($this->config->getType() == \Magento\PageCache\Model\Config::BUILT_IN && $this->config->isEnabled()) {
            $ruleDesignException = $this->designExceptions->getThemeForUserAgent($this->request);
            if ($ruleDesignException !== false) {
                return $ruleDesignException . $result;
            }
        }
        return $result;
    }
}
