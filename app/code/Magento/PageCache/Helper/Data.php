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
}
