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
 * @category   Mage
 * @package    Mage_Oscommerce
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * osCommerce resource model
 *
 * @author     Kyaw Soe Lynn Maung <vincent@varien.com>
 */
class Mage_Oscommerce_Model_Mysql4_Oscommerce_Order_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('oscommerce/oscommerce_order');
    }

    public function addOrderTotalField()
    {
        $this->_select
            ->from(null, array('orders_total'=>new Zend_Db_Expr('FORMAT(main_table.orders_total,2)')));
        return $this;
    }    
    
    public function load($printQuery=false, $logQuery=false)
    {
        if ($this->isLoaded()) {
            return $this;
        }
        $this->addOrderTotalField();
        parent::load($printQuery, $logQuery);
        return $this;
    }    
}
