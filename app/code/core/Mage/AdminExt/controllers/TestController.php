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
 * @package    Mage_AdminExt
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test controller
 *
 * @category   Mage
 * @package    Mage_AdminExt
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Admin_TestController extends Mage_Core_Controller_Zend_Action 
{
    public function wizardAction()
    {
        $step = $this->getRequest()->getParam('step');
        
        switch ($step) {
            case 1:
                $customer = Mage::getModel('customer/customer');
                $form = new Mage_Admin_Block_Customer_Form($customer);

                $tab = array(
                    'name'  => 'general',
                    'title' => __('Account Information'),
                    'type'  => 'form',
                    'form'  => $form->toArray()
                );
                break;
            case 2:
                $address = Mage::getModel('customer/address');
                $form = new Mage_Admin_Block_Customer_Address_Form($address);

                $tab = array(
                    'name'  => 'general',
                    'title' => __('Customer Address'),
                    'type'  => 'form',
                    'form'  => $form->toArray()
                );
                break;
            default:
                $tab['title'] = __('Add New Customer');
                $tab['name']  = 'default';
                $tab['type']  = 'view';
                $tab['url']   = Mage::getBaseUrl();
                break;
        }
        
        $cardStruct['title'] = __('Add New Customer');
        $cardStruct['error'] = 0;
        $cardStruct['tabs'][] = $tab;
        $this->getResponse()->setBody(Zend_Json::encode($cardStruct));
    }
    
    public function backupAction()
    {
        $tables = Mage::getSingleton('backup/db')->renderSql();
        echo '<pre>';
        print_r($tables);
        echo '</pre>';
    }
}