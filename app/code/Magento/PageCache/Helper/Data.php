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
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\PageCache\Helper;

/**
 * Class Data
 * @package Magento\PageCache\Helper
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
