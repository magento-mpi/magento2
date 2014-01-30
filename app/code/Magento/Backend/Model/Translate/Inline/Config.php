<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend Inline Translation config
 */
namespace Magento\Backend\Model\Translate\Inline;

class Config implements \Magento\Translate\Inline\ConfigInterface
{
    /**
     * @var \Magento\Backend\App\ConfigInterface
     */
    protected $_config;

    /**
     * @var \Magento\Core\Helper\Data
     */
    protected $_helper;

    /**
     * @param \Magento\Backend\App\ConfigInterface $config
     * @param \Magento\Core\Helper\Data $helper
     */
    public function __construct(
        \Magento\Backend\App\ConfigInterface $config,
        \Magento\Core\Helper\Data $helper
    ) {
        $this->_config = $config;
        $this->_helper = $helper;
    }

    /**
     * Check whether inline translation is enabled
     *
     * @param int|null $store
     * @return bool
     */
    public function isActive($store = null)
    {
        return $this->_config->isSetFlag('dev/translate_inline/active_admin');
    }

    /**
     * Check whether allowed client ip for inline translation
     *
     * @param mixed $store
     * @return bool
     */
    public function isDevAllowed($store = null)
    {
        return $this->_helper->isDevAllowed($store);
    }
}
