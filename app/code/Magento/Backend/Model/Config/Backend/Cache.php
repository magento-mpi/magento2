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
 * Cache cleaner backend model
 *
 */
namespace Magento\Backend\Model\Config\Backend;

class Cache extends \Magento\App\Config\Value
{
    /**
     * Cache tags to clean
     *
     * @var array
     */
    protected $_cacheTags = array();

    /**
     * Clean cache, value was changed
     *
     * @return void
     */
    protected function _afterSave()
    {
        if ($this->isValueChanged()) {
            $this->_cacheManager->clean($this->_cacheTags);
        }
    }
}
