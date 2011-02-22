<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @category    Enterprise
 * @package     Enterprise_Customer
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Customer Quote Address resource model
 *
 * @category    Enterprise
 * @package     Enterprise_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Customer_Model_Resource_Sales_Quote_Address
    extends Enterprise_Customer_Model_Resource_Sales_Address_Abstract
{
    /**
     * Main entity model name
     */
    protected $_parentModelName = 'sales/quote_address';

    /**
     * Main entity resource model
     * @var Mage_Sales_Model_Resource_Quote_Address
     */
    protected $_parentResourceModel = null;

    /**
     * Initialize resource
     *
     */
    protected function _construct()
    {
        $this->_init('enterprise_customer/sales_quote_address', 'entity_id');
    }

    /**
     * Return resource model of the main entity
     *
     * @return Mage_Sales_Model_Resource_Quote_Address
     */
    protected function _getParentResourceModel()
    {
        if (is_null($this->_parentResourceModel)) {
            $this->_parentResourceModel = Mage::getModel($this->_parentModelName)->getResource();
        }
        return $this->_parentResourceModel;
    }

    /**
     * Check if main entity exists in main table.
     * Need to prevent errors in case of multiple customer log in into one account.
     *
     * @param Enterprise_Customer_Model_Sales_Abstract $sales
     * @return bool
     */
    public function isEntityExists(Enterprise_Customer_Model_Sales_Abstract $sales)
    {
        if (!$sales->getId()) {
            return false;
        }

        $parentTable = $this->_getParentResourceModel()->getMainTable();
        $parentIdField = $this->_getParentResourceModel()->getIdFieldName();
        $select = $this->_getWriteAdapter()->select()
            ->from($parentTable, $parentIdField)
            ->forUpdate(true)
            ->where("{$parentIdField} = ?", $sales->getId());

        if ($this->_getWriteAdapter()->fetchOne($select)){
            return true;
        }
        return false;
    }
}
