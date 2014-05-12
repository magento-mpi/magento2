<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Page cache data helper
 *
 */
namespace Magento\PageCache\Helper;

/**
 * Helper for Page Cache module
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Private caching time one year
     */
    const PRIVATE_MAX_AGE_CACHE = 31536000;

    /**
     * @var \Magento\Framework\App\View
     */
    protected $view;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\View $view
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\View $view
    ) {
        parent::__construct($context);
        $this->view = $view;
    }

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
        return $this->view->getLayout()->getUpdate()->getHandles();
    }
}
