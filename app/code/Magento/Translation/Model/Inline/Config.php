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
     * @var \Magento\Core\Model\Store\ConfigInterface
     */
    protected $config;

    /**
     * @var \Magento\Core\Helper\Data
     */
    protected $_helper;

    /**
     * @param \Magento\Core\Model\Store\ConfigInterface $config
     * @param \Magento\Core\Helper\Data $helper
     */
    public function __construct(\Magento\Core\Model\Store\ConfigInterface $config, \Magento\Core\Helper\Data $helper)
    {
        $this->config = $config;
        $this->_helper = $helper;
    }

    /**
     * @inheritdoc
     */
    public function isActive($scope = null)
    {
        return $this->config->getConfigFlag('dev/translate_inline/active', $scope);
    }

    /**
     * @inheritdoc
     */
    public function isDevAllowed($scope = null)
    {
        return $this->_helper->isDevAllowed($scope);
    }
}
