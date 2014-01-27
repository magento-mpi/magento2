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
     * Location in XML config
     */
    const MAX_AGE_PATH = 'system/headers/max-age';

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
    public function getMaxAgeCache()
    {
        return $this->config->getValue(self::MAX_AGE_PATH);
    }
}
