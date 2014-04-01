<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Translation\Model\Inline;

/**
 * Inline Translation config
 */
class Config implements \Magento\Translate\Inline\ConfigInterface
{
    /**
     * Core store config
     *
     * @var \Magento\App\Config\ScopeConfigInterface
     */
    protected $_storeConfig;

    /**
     * @var \Magento\Core\Helper\Data
     */
    protected $_helper;

    /**
     * @param \Magento\App\Config\ScopeConfigInterface $coreStoreConfig
     * @param \Magento\Core\Helper\Data $helper
     */
    public function __construct(
        \Magento\App\Config\ScopeConfigInterface $coreStoreConfig,
        \Magento\Core\Helper\Data $helper
    ) {
        $this->_storeConfig = $coreStoreConfig;
        $this->_helper = $helper;
    }

    /**
     * @inheritdoc
     */
    public function isActive($scope = null)
    {
        return $this->_storeConfig->isSetFlag(
            'dev/translate_inline/active',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $scope
        );
    }

    /**
     * @inheritdoc
     */
    public function isDevAllowed($scope = null)
    {
        return $this->_helper->isDevAllowed($scope);
    }
}
