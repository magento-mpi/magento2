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
 * API2 Instance resource model
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Api2_Model_Resource_Instance extends Mage_Api2_Model_Resource
{
    /**
     * Internal "instance" resource model dispatch
     */
    public function dispatch()
    {
        switch ($this->getRequest()->getOperation()) {
            case self::OPERATION_CREATE:
                $this->_create(array());
                break;
            case self::OPERATION_UPDATE:
                $requestData  = $this->getRequest()->getBodyParams();
                $filteredData = $this->getFilter()->in($requestData);

                $this->_update($filteredData);
                break;
            case self::OPERATION_RETRIEVE:
                $retrievedData = $this->_retrieve();
                $filteredData  = $this->getFilter()->out($retrievedData);

                $this->_render($filteredData);
                break;
            case self::OPERATION_DELETE:
                $this->_delete();
                break;
            default:
                $this->_critical(self::RESOURCE_METHOD_NOT_IMPLEMENTED);
                break;
        }
    }

    /**
     * Create method not allowed for this type of resource
     *
     * @param array $data
     */
    final protected function _create(array $data)
    {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * Get available attributes of API resource from configuration file
     *
     * @return array
     * @throw Exception
     */
    public function getAvailableAttributesFromConfig()
    {
        return $this->getConfig()->getResourceAttributes($this->getResourceType());
    }

    /**
     * Get available attributes of API resource
     * Most common way
     *
     * @param string $userType
     * @param string $operation
     * @return array
     */
    protected function _getAvailableAttributes($userType = null, $operation = null)
    {
        /** @var $resourceModel Mage_Core_Model_Resource_Db_Abstract */
        $resourceModel  = Mage::getResourceModel($this->getConfig()->getResourceWorkingModel($this->getResourceType()));

        $attrCodes = array_keys($resourceModel->getReadConnection()->describeTable($resourceModel->getMainTable()));
        $attributes = $this->getAvailableAttributesFromConfig();
        $excluded = $this->getExcludedAttributes($userType, $operation);

        $available = array();
        foreach ($attrCodes as $code) {
            if (in_array($code, $excluded)) {
                continue;
            }

            if (isset($attributes[$code])) {
                $label = $attributes[$code];
            } else {
                $label = $code;
            }
            $available[$code] = $label;
        }

        return $available;
    }
}
