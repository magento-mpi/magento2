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
 * Customer collection for alerts
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author     Vasily Selivanov <vasily@varien.com>
 */

class Mage_CustomerAlert_Model_Mysql4_Customer_Collection extends Mage_Customer_Model_Entity_Customer_Collection
{
    protected $_alert;
    
    public function setAlert(Mage_CustomerAlert_Model_Type $alert)
    {
        $this->_alert = $alert;
        $this->joinField('alerts','customer_product_alert','product_id','customer_id=entity_id',$this->_alert->getParamValues())
            /*->joinField('check','customer_product_alert_check','id','product_id=alerts',$this->_alert->getParamValues(),'left')
            ->joinField('last_alert_sent','customer_product_alert_queue','check_id','check_id=check',null,'left')*/
            ->addAttributeToSelect('*');
        return $this;
        
    }
    

}