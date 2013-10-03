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
 * Config source reports event store filter
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Model\Config\Source\Reports;

class Scope implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * Scope filter
     */
    public function toOptionArray()
    {
        return array(
            array('value'=>'website', 'label'=>__('Website')),
            array('value'=>'group', 'label'=>__('Store')),
            array('value'=>'store', 'label'=>__('Store View')),
        );
    }

}
