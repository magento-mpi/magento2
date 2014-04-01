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
     * @var \Magento\App\Config\ScopeConfigInterface
     */
    protected $_storeConfig;

    /**
     * View url config model
     *
     * @param \Magento\App\Config\ScopeConfigInterface $storeConfig
     */
    public function __construct(\Magento\App\Config\ScopeConfigInterface $storeConfig)
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
        return $this->_storeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
