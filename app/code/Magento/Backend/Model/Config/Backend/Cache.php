<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Cache cleaner backend model
 *
 */
namespace Magento\Backend\Model\Config\Backend;

class Cache extends \Magento\Framework\App\Config\Value
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
    public function afterSave()
    {
        if ($this->isValueChanged()) {
            $this->_cacheManager->clean($this->_cacheTags);
        }
    }
}
