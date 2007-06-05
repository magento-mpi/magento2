<?php
/**
 * Test controller
 *
 * @package     Mage
 * @subpackage  Admin
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Admin_TestController extends Mage_Core_Controller_Zend_Action 
{
    public function wizardAction()
    {
        $step = $this->getRequest()->getParam('step');
        
        switch ($step) {
            case 1:
                $customer = Mage::getModel('customer', 'customer');
                $form = new Mage_Admin_Block_Customer_Form($customer);

                $tab = array(
                    'name'  => 'general',
                    'title' => __('Account Information'),
                    'type'  => 'form',
                    'form'  => $form->toArray()
                );
                break;
            case 2:
                $address = Mage::getModel('customer', 'address');
                $form = new Mage_Admin_Block_Customer_Address_Form($address);

                $tab = array(
                    'name'  => 'general',
                    'title' => __('Customer address'),
                    'type'  => 'form',
                    'form'  => $form->toArray()
                );
                break;
            default:
                $tab['title'] = __('New Customer');
                $tab['name']  = 'default';
                $tab['type']  = 'view';
                $tab['url']   = Mage::getBaseUrl();
                break;
        }
        
        $cardStruct['title'] = __('New Customer');
        $cardStruct['tabs'][] = $tab;
        $this->getResponse()->setBody(Zend_Json::encode($cardStruct));
    }
}
