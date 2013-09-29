<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Store switcher block
 *
 * @category   Magento
 * @package    Magento_VersionsCms
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\VersionsCms\Block\Adminhtml\Scope;

class Switcher extends \Magento\Backend\Block\System\Config\Switcher
{
    /**
     * Scope switcher options
     *
     * @var array
     */
    protected $_options = null;

    /**
     * Get scope switcher options
     *
     * @return array
     */
    public function getStoreSelectOptions()
    {
        if (is_null($this->_options)) {
            $this->_options = parent::getStoreSelectOptions();
            $this->_options['default']['label'] = __('All Store Views');
        }

        return $this->_options;
    }

    /**
     * Get switcher default option value
     *
     * @return string
     */
    public function getDefaultValue()
    {
        foreach ($this->getStoreSelectOptions() as $value => $option) {
            if (array_key_exists('selected', $option) && $option['selected']) {
                return $value;
            }
        }

        return '';
    }

    /**
     * Retrieve block HTML markup
     *
     * @return string
     */
    protected function _toHtml()
    {
        return \Mage::app()->isSingleStoreMode() == false ? parent::_toHtml() : '';
    }
}
