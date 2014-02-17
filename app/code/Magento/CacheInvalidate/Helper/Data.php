<?php
/**
 * Page cache data helper
 *
 * @category    Magento
 * @package     Magento_CacheInvalidate
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CacheInvalidate\Helper;

/**
 * Class Data
 *
 * @package Magento\CacheInvalidate\Helper
 */
class Data extends \Magento\App\Helper\AbstractHelper
{
    /**
     * Retrieve url
     *
     * @param $route
     * @param array $params
     * @return string
     */
    public function getUrl($route, $params = array())
    {
        return $this->_getUrl($route, $params);
    }
}
