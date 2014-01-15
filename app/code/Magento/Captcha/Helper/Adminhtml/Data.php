<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Captcha
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Captcha helper for adminhtml area
 *
 * @category   Magento
 * @package    Magento_Captcha
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Captcha\Helper\Adminhtml;

class Data extends \Magento\Captcha\Helper\Data
{
    /**
     * @var \Magento\Backend\App\ConfigInterface
     */
    protected $_backendConfig;

    /**
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\Core\Model\StoreManager $storeManager
     * @param \Magento\App\ConfigInterface $config
     * @param \Magento\Filesystem $filesystem
     * @param \Magento\Captcha\Model\CaptchaFactory $factory
     * @param \Magento\Backend\App\ConfigInterface $backendConfig
     */
    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\Core\Model\StoreManager $storeManager,
        \Magento\App\ConfigInterface $config,
        \Magento\Filesystem $filesystem,
        \Magento\Captcha\Model\CaptchaFactory $factory,
        \Magento\Backend\App\ConfigInterface $backendConfig
    ) {
        $this->_backendConfig = $backendConfig;
        parent::__construct($context, $storeManager, $config, $filesystem, $factory);
    }


    /**
     * Returns config value for admin captcha
     *
     * @param string $key The last part of XML_PATH_$area_CAPTCHA_ constant (case insensitive)
     * @param \Magento\Core\Model\Store $store
     * @return \Magento\Core\Model\Config\Element
     */
    public function getConfig($key, $store = null)
    {
        return $this->_backendConfig->getValue('admin/captcha/' . $key);
    }

    /**
     * Get website code
     *
     * @param mixed $website
     * @return string
     */
    protected function _getWebsiteCode($website = null)
    {
        return 'admin';
    }
}
