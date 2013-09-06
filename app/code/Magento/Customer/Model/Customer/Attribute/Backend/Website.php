<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Website attribute backend
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Customer_Model_Customer_Attribute_Backend_Website extends Magento_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Before save
     *
     * @param Magento_Object $object
     * @return Magento_Customer_Model_Customer_Attribute_Backend_Website
     */
    public function beforeSave($object)
    {
        if ($object->getId()) {
            return $this;
        }

        if (!$object->hasData('website_id')) {
            $object->setData('website_id', Mage::app()->getStore()->getWebsiteId());
        }

        return $this;
    }
}
