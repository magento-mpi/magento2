<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Page cache data helper
 *
 * @category    Magento
 * @package     Magento_PageCache
 */
namespace Magento\PageCache\Helper;

/**
 * Helper for Page Cache module
 */
class Data extends \Magento\App\Helper\AbstractHelper
{
    /**
     * Constructor
     *
     * @param \Magento\Theme\Model\Layout\Config $config
     * @param \Magento\App\View                  $view
     */
    public function __construct(
        \Magento\Theme\Model\Layout\Config $config,
        \Magento\App\View $view
    ) {
        $this->view = $view;
        $this->config = $config;
    }

    /**
     * Private caching time one year
     */
    const PRIVATE_MAX_AGE_CACHE = 31536000;

    /**
     * Retrieve url
     *
     * @param string $route
     * @param array $params
     * @return string
     */
    public function getUrl($route, array $params = array())
    {
        return $this->_getUrl($route, $params);
    }

    /**
     * Get handles applied for current page
     *
     * @return array
     */
    public function getActualHandles()
    {
        $handlesPage = $this->view->getLayout()->getUpdate()->getHandles();
        $handlesConfig = $this->config->getPageLayoutHandles();
        $appliedHandles = array_intersect($handlesPage, $handlesConfig);
        $resultHandles = array_merge(['default'], array_values($appliedHandles));

        return $resultHandles;
    }
}
