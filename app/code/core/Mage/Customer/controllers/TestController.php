<?php
/**
 * test
 *
 * @package     Core
 * @subpackage  Core
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Customer_TestController extends Mage_Core_Controller_Front_Action 
{
    public function indexAction()
    {
        Varien_Profiler::start('entity-init');
        $entity = Mage::getModel('eav/entity')->setType('customer')->loadAllAttributes();
        Varien_Profiler::stop('entity-init');

        $customer = Mage::getModel('customer/customer');

        Varien_Profiler::start('entity-save');
        $customer->setFirstname('Moshe')->setLastname('Gurvich')->setEmail('moshe@varien.com')
            ->setCustomerGroup(1)->setStoreBalance(12.34)
            ->setCreatedAt(now())->setUpdatedAt(now());
        $entity->save($customer);
        Varien_Profiler::stop('entity-save');
        
        $customerId = $customer->getEntityId();
        
        $customer = Mage::getModel('customer/customer');
        
        Varien_Profiler::start('entity-load');
        $entity->load($customer, $customerId);
        Varien_Profiler::stop('entity-load');

        echo '<pre>';
        print_r($customer->getData());
        echo '</pre>';
        
        Varien_Profiler::start('entity-collection');
        $collection = Mage::getModel('eav/entity_collection')
            ->setEntity($entity)->setObject($customer)
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('firstname', array('like'=>'mos%'))
            #->addAttributeToSort('lastname')
            ->setPage(1,10)
            ->load();
        Varien_Profiler::stop('entity-collection');

        echo '<pre>';
        print_r($collection->exportToArray());
        echo '</pre>';
        
        
        Varien_Profiler::start('entity-delete');
        #$collection->delete();
        Varien_Profiler::stop('entity-delete');

        echo $this->getLayout()->createBlock('core/profiler')->toHtml();
    }
    
    public function eavAction()
    {
        $model = Mage::getModel('customer/customer');
        $model->load(1);
        $model->save();
        /*
        $model->setFirstname('moshe')->setLastname('gurvich')->setEmail('moshe@varien.com')->setStoreId(1)->setAttributeSetId(1);
        $model->save();
        */
        
        
        print_r($model->getData());
    }
    
    public function fillAction()
    {
        set_time_limit(0);
        for ($i=2000; $i<=5000; $i++){
            $customerData = array(
                'firstname' => 'FN #'.$i,
                'lastname'  => 'LN #'.$i,
                'email'     => 'email'.$i.'@domain.com',
                'password_hash' => md5('123123'),
                'customer_group'=> 1,
                'store_balance' => 100+$i,
                'default_billing'   => 'new1',
                'default_shipping'  => 'new2'
            );
            
            $customer = Mage::getModel('customer/customer')
                ->setData($customerData);
            for ($j=1;$j<=rand(3,10);$j++){
                $addressData = array(
                    'firstname'     => 'AFN #'.$j.'_'.$i,
                    'lastname'      => 'ALN #'.$j.'_'.$i,
                    'company'       => 'COMPANY #'.$j.'_'.$i,
                    'country_id'    => 223,
                    'region'        => 'California',
                    'region_id'     => 12,
                    'postcode'      => '09034',
                    'city'          => 'LA',
                    'street'        => 'Motor,'.$i,
                    'telephone'     => '(301) 112-12'.$i,
                    'fax'           => '(301) 112-12'.$i,
                );
                $address = Mage::getModel('customer/address')
                    ->setData($addressData)
                    ->setPostIndex('new'.$j);
                $customer->addAddress($address);
            }
            /*echo '<pre>';
            print_r($customer->getAddressCollection()->getItems());
            echo '</pre>';*/
            $customer->save();
        }
    }
    
    public function gridAction()
    {
        $collection = Mage::getResourceModel('customer/customer_collection')
            ->addAttributeToSelect('firstname')
            ->addAttributeToSelect('lastname')
            ->addAttributeToSelect('email')
            ->load();
        echo '<pre>';
        print_r($collection->getSize());
        echo '</pre>';
    }
}
