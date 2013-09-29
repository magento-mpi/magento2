<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Contacts
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Cache cleaner backend model
 *
 */
namespace Magento\Contacts\Model\System\Config\Backend;

class Links extends \Magento\Backend\Model\Config\Backend\Cache
{
    /**
     * Cache tags to clean
     *
     * @var array
     */
    protected $_cacheTags = array(\Magento\Core\Model\Store::CACHE_TAG, \Magento\Cms\Model\Block::CACHE_TAG);

}
