<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 *Quote address attribute backend parent resource model
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Model\Resource\Quote\Address\Attribute\Backend;

class Parent
    extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * Save items collection and shipping rates collection
     *
     * @param \Magento\Object $object
     * @return \Magento\Sales\Model\Resource\Quote\Address\Attribute\Backend\Parent
     */
    public function afterSave($object)
    {
        parent::afterSave($object);
        
        $object->getItemsCollection()->save();
        $object->getShippingRatesCollection()->save();
        
        return $this;
    }
}
