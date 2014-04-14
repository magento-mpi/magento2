<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\PageCache\Model\App;

/**
 * Class CachePlugin
 * Should add design exceptions o identifier for built-in cache
 */
class CacheIdentifierPlugin
{
    /**
     * Constructor
     *
     * @param \Magento\View\DesignExceptions  $designExceptions
     * @param \Magento\App\RequestInterface   $request
     * @param \Magento\PageCache\Model\Config $config
     */
    public function __construct(
        \Magento\View\DesignExceptions $designExceptions,
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
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetValue(\Magento\App\PageCache\Identifier $identifier, $result)
    {
        if ($this->config->getType() == \Magento\PageCache\Model\Config::BUILT_IN && $this->config->isEnabled()) {
            $ruleDesignException = $this->designExceptions->getThemeByRequest($this->request);
            if ($ruleDesignException !== false) {
                return $ruleDesignException . $result;
            }
        }
        return $result;
    }
}
