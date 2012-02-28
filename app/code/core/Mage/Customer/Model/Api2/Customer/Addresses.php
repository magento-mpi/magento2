<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Api2
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * API2 class for customer addresses
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Model_Api2_Customer_Addresses extends Mage_Api2_Model_Resource_Collection
{
    /**
     * Get available attributes of API resource
     *
     * @param string $userType
     * @param string $operation
     * @return array
     */
    public function getAvailableAttributes($userType, $operation)
    {
        $attributes = parent::getAvailableAttributesFromConfig();

        /* @var $entityType Mage_Eav_Model_Entity_Type */
        $entityType = Mage::getModel('eav/entity_type')->loadByCode('customer_address');

        /* @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
        foreach ($entityType->getAttributeCollection() as $attribute) {
            if ($attribute->getIsVisible()) {
                $attributes[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();
            }
        }

        return $attributes;
    }
}
