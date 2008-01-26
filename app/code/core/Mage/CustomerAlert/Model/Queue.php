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
 * @package    Mage_CustomerAlert
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Alerts queue model
 *
 * @category   Mage
 * @package    Mage_CustomerAlert
 * @author     Vasily Selivanov <vasily@varien.com>
 */

class Mage_CustomerAlert_Model_Queue extends Mage_Core_Model_Abstract
{
    const STATUS_NEVER = 0;
    const STATUS_SENDING = 1;
    const STATUS_CANCEL = 2;
    const STATUS_SENT = 3;
    const STATUS_PAUSE = 4;

    public function __construct()
    {
        $this->_init('customeralert/queue');
    }

    public function addCustomersToAlertQueue(Mage_CustomerAlert_Model_Mysql4_Customer_Collection $customer, array $check)
    {
        if(is_array($check)){
            foreach ($check as $id) {
                $check = Mage::getModel('customeralert/alert_check')
                   ->load($id);
                $type = $check->getType();
                $emailModel = Mage::getModel('core/email_template')
                    ->loadByCode(Mage::getSingleton('customeralert/config')->getDefaultTemplateForAlert($type));
                if($emailModel->getId()){
                    $this->addData(array('template_id'=>$emailModel->getId()));
                }
                if($check->getId()){
                    $this->addData(array('check_id'=>$check->getId()));
                }
                $this->save();
                Mage::getResourceModel('customeralert/queue')
                    ->addCustomersToAlertQueue($this, $customer);
            }
        }
        return true;
    }

    public function addTemplateData( $data )
    {
        if ($data->getTemplateId()) {
            $this->setTemplate(Mage::getModel('core/email_template')
                ->load($data->getTemplateId()));
        }
        return $this;
    }

    public function getCheck()
    {
        return Mage::getModel('customeralert/alert_check')->load($this->getCheckId());
    }

    public function getProduct()
    {
        return Mage::getModel('catalog/product')->load($this->getCheck()->getProductId());
    }

}
