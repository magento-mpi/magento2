<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Country customer grid column filter
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Customer\Grid\Filter;

class Country extends \Magento\Adminhtml\Block\Widget\Grid\Column\Filter\Select
{

    protected function _getOptions()
    {
        $options = \Mage::getResourceModel('Magento\Directory\Model\Resource\Country\Collection')->load()->toOptionArray();
        array_unshift($options, array('value'=>'', 'label'=>__('All countries')));
        return $options;
    }

}
