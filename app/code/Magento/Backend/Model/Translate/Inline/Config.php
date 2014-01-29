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

class Config implements \Magento\Core\Model\Translate\Inline\ConfigInterface
{
    /**
     * @var \Magento\Backend\App\ConfigInterface
     */
    protected $_config;

    /**
     * @param \Magento\Backend\App\ConfigInterface $config
     */
    public function __construct(\Magento\Backend\App\ConfigInterface $config)
    {
        $this->_config = $config;
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
}
