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
 * Country grid filter
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Block\Widget\Grid\Column\Filter;

class Country extends \Magento\Backend\Block\Widget\Grid\Column\Filter\Select
{
    protected function _getOptions()
    {
        $options = \Mage::getResourceModel('\Magento\Directory\Model\Resource\Country\Collection')
            ->load()
            ->toOptionArray(false);
        array_unshift($options,
            array('value'=>'', 'label'=>__('All Countries'))
        );
        return $options;
    }
}
