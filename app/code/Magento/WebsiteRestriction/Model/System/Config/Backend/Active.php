<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Cleanup blocks HTML cache
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\WebsiteRestriction\Model\System\Config\Backend;

class Active extends \Magento\Framework\App\Config\Value
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'magento_websiterestriction_config_active';

    /**
     * Cleanup blocks HTML cache if value has been changed
     *
     * @return $this
     */
    protected function _afterSave()
    {
        if ($this->isValueChanged()) {
            $this->_cacheManager->clean(
                array(\Magento\Store\Model\Store::CACHE_TAG, \Magento\Cms\Model\Block::CACHE_TAG)
            );
        }
        return parent::_afterSave();
    }
}
