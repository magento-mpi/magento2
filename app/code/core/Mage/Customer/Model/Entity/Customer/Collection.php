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
 * @package    Mage_Customer
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customers collection
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Customer_Model_Entity_Customer_Collection extends Mage_Eav_Model_Entity_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('customer/customer');
    }

    public function groupByEmail()
    {
        $this->getSelect()
            ->from(array('email'=>$this->getEntity()->getEntityTable()),
                array('email_count'=>new Zend_Db_Expr('COUNT(email.entity_id)'))
            )
            ->where('email.entity_id=e.entity_id')
            ->group('email.email');
        return $this;
    }
}