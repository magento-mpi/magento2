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
 * @package     Mage_Core
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Custom variabel collection
 *
 * @category   Mage
 * @package    Mage_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Mysql4_Email_Variable_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected $_storeId = 0;

    /**
     * Constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('core/email_variable');
    }

    /**
     * Setter
     *
     * @param integer $storeId
     * @return Mage_Core_Model_Mysql4_Email_Variable_Collection
     */
    public function setStoreId($storeId)
    {
        $this->_storeId = $storeId;
        return $this;
    }

    /**
     * Getter
     *
     * @return integer
     */
    public function getStoreId()
    {
        return $this->_storeId;
    }

    /**
     * Add store values to result
     *
     * @return Mage_Core_Model_Mysql4_Email_Variable_Collection
     */
    public function addValuesToResult()
    {
        $this->getSelect()
            ->join(
                array('value_table' => $this->getTable('core/email_variable_value')),
                $this->getConnection()->quoteInto('value_table.variable_id = main_table.variable_id AND store_id = ?', $this->getStoreId()),
                array())
            ->columns(array('value' => 'value_table.value'));
        return $this;
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('code', 'name');
    }

}
