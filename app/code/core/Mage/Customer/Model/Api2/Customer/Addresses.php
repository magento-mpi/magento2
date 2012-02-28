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
        $available     = array();
        $configAttrs   = $this->getAvailableAttributesFromConfig();
        $excludedAttrs = $this->getExcludedAttributes($userType, $operation);
        $dbAttrs = $this->getDbAttributes();
        $eavAttrs = $this->getEavAttributes();
        $attrsCodes = array_merge(array_keys($configAttrs), $dbAttrs, array_keys($eavAttrs));

        foreach ($attrsCodes as $code) {
            if (in_array($code, $excludedAttrs)) {
                continue;
            }
            if (isset($configAttrs[$code])) {
                // first priority
                $available[$code] = $configAttrs[$code];
            } elseif (isset($eavAttrs[$code])) {
                $available[$code] = $eavAttrs[$code];
            } else {
                $available[$code] = $code;
            }
        }

        return $available;
    }

    /**
     * Get available attributes of API resource from data base
     *
     * @return array
     */
    public function getDbAttributes()
    {
        $available     = array();
        /* @var $resource Mage_Core_Model_Resource_Db_Abstract */
        $resource = Mage::getResourceModel($this->getConfig()->getResourceWorkingModel($this->getResourceType()));
        if (method_exists($resource, 'getEntityTable')) {
            $available = array_keys($resource->getReadConnection()->describeTable($resource->getEntityTable()));
        }
        return $available;
    }
}
