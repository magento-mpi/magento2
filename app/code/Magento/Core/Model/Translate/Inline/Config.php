<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Inline Translation config
 */
namespace Magento\Core\Model\Translate\Inline;

class Config implements \Magento\Translate\Inline\ConfigInterface
{
    /**
     * Core store config
     *
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_coreStoreConfig;

    /**
     * @var \Magento\Core\Helper\Data
     */
    protected $_helper;

    /**
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Core\Helper\Data $helper
     */
    public function __construct(
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Core\Helper\Data $helper
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_helper = $helper;
    }

    /**
     * @inheritdoc
     */
    public function isActive($scope = null)
    {
        return $this->_coreStoreConfig->getConfigFlag('dev/translate_inline/active', $scope);
    }

    /**
     * @inheritdoc
     */
    public function isDevAllowed($scope = null)
    {
        return $this->_helper->isDevAllowed($scope);
    }
}
