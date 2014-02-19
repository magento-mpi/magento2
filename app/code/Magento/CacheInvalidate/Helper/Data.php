<?php
/**
 * {license_notice}
 *
 * Page cache data helper
 *
 * @category    Magento
 * @package     Magento_CacheInvalidate
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CacheInvalidate\Helper;

/**
 * Class Data
 */
class Data extends \Magento\App\Helper\AbstractHelper
{
    /**
     * Retrieve url
     *
     * @param string $route
     * @param array $params
     * @return string
     */
    public function getUrl($route, $params = array())
    {
        return $this->_getUrl($route, $params);
    }
}
