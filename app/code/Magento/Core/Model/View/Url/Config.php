<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\View\Url;

class Config implements \Magento\View\Url\ConfigInterface
{
    /**
     * @var \Magento\Store\Model\Config
     */
    protected $_storeConfig;

    /**
     * View url config model
     *
     * @param \Magento\Store\Model\Config $storeConfig
     */
    public function __construct(\Magento\Store\Model\Config $storeConfig)
    {
        $this->_storeConfig = $storeConfig;
    }

    /**
     * Retrieve url store config value
     *
     * @param string $path
     * @return mixed
     */
    public function getValue($path)
    {
        return $this->_storeConfig->getConfig($path);
    }
}
