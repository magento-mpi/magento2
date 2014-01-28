<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Page cache data helper
 *
 * @category    Magento
 * @package     Magento_PageCache
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\PageCache\Helper;

/**
 * Class Data
 * @package Magento\PageCache\Helper
 */
class Data extends \Magento\App\Helper\AbstractHelper
{
    /**
     * Private caching time one year
     */
    const PRIVATE_MAX_AGE_CACHE = 31536000;

    /**
     * XML path to value for public max-age parameter
     */
    const PUBLIC_MAX_AGE_PATH = 'system/headers/public-max-age';

    /**
     * @var \Magento\Core\Model\ConfigInterface
     */
    protected $config;

    /**
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\Core\Model\ConfigInterface $config
     */
    public function __construct(\Magento\App\Helper\Context $context, \Magento\Core\Model\ConfigInterface $config)
    {
        parent::__construct($context);
        $this->config = $config;
    }

    /**
     * @return mixed
     */
    public function getPublicMaxAgeCache()
    {
        return $this->config->getValue(self::PUBLIC_MAX_AGE_PATH);
    }
}
