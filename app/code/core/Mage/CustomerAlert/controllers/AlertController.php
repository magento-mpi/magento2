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
 * Customer alert controller
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author     Vasily Selivanov <vasily@varien.com>
 */
class Mage_CustomerAlert_AlertController extends Mage_Core_Controller_Front_Action
{
    public function saveAlertsAction()
    {
        $data = array();
        $data['customer_id'] = Mage::getModel('customer/session')->getId();
        $params = $this->getRequest()->getParams();
        if(!isset($params[Mage_Core_Controller_Front_Action::PARAM_NAME_BASE64_URL])) {
            Mage::getModel('catalog/session')->addError(__('Not enough parameters'));
            $this->_redirect('/');
            return false;
        } else {
            $backUrl = base64_decode($params[Mage_Core_Controller_Front_Action::PARAM_NAME_BASE64_URL]);
        }
        if($data['customer_id']){
            $alertType = $params['type']; 
            if(isset($params['product_id']) && $alertType){
                $data['product_id'] = $params['product_id'];
                $data['store_id'] = Mage::app()->getStore()->getId();
                try{
                    Mage::getModel('customeralert/config')->getAlertByType($alertType)   
                                    ->addData($data)
                                    ->save();
                    Mage::getModel('catalog/session')->addSuccess(__('Alert was saved successfuly.'));
                } catch (Exception $e) {
                    Mage::getModel('catalog/session')->addError(__('Alert was not saved. %s',$e->getMessage()));
                }
                $this->_redirectUrl($backUrl);
            } else {
                Mage::getModel('catalog/session')->addError(__('Not enough parameters'));
                $this->_redirectUrl($backUrl);
            }
        } else {
            Mage::getModel('catalog/session')->addError(__('Your are not logged in'));
            $this->_redirectUrl($backUrl);
        }
    }
}