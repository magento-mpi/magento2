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
 * @category   Mage
 * @package    Mage_Tests
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

if (!defined('_IS_INCLUDED')) {
    require dirname(__FILE__) . '/../../../PHPUnitTestInit.php';
    PHPUnitTestInit::runMe(__FILE__);
}

class Webservice_Sales_Order_ShipmentTest extends WebService_TestCase_Abstract
{
    protected $_order;
    protected $_customer;
    protected $_product;
    protected $_createShipmentId;
    
    protected function _initCustomer()
    {
        $newCustomerData = array(
              'firstname'  => 'First',
              'lastname'   => 'Last',
              'email'      => 'test@example.com',
              'password_hash'   => md5('password'),
              'store_id'   => 0,
              'website_id' => 0
            );
        $customer = Mage::getModel('customer/customer')->setData($newCustomerData); 
        $customer->save();   
        $this->_customer = $customer;
    }
    
    protected function _initProduct()
    {
       $product = Mage::getModel('catalog/product');
       $product->setStoreId(Mage::app()->getStore())
            ->setAttributeSetId(current($this->_getDefaultAttributeSet()))
            ->setTypeId('simple')
            ->setSku('simple sku');
       $product->setData('short_description','short_description');     
       $product->setData('name','soap product');     
       $product->setData('description','description');     
       $product->setData('price',12.05);     
       $product->setWebsiteIds(array('base'));    
       $product->save();
       
       $this->_product = $product;
    }
    
    protected function _getDefaultAttributeSet()
    {
        $entityType = Mage::getModel('catalog/product')->getResource()->getEntityType();
        $collection = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter($entityType->getId());

        $result = array();
        foreach ($collection as $attributeSet) {
            $result[] = array(
                'set_id' => $attributeSet->getId(),
                'name'   => $attributeSet->getAttributeSetName()
            );

        }
        return $result;
    }
    
    protected function _initOrder()
    {
        $this->_order = '100000016';
    }
    /**
     * @dataProvider connectorProvider
     */
    public function testResultIsArray(WebService_Connector_Interface $connector)
    {
        $serviceResult = $connector->call('sales_order_shipment.list');
        $this->assertType('array', $serviceResult);
        
    }
    
    /**
     * @dataProvider connectorProvider
     */
    public function testCreateShipment(WebService_Connector_Interface $connector)
    {
        $this->_initOrder();
        $data = array($this->_order,array('17'=>1),'SOAP comment',false,true);
        $this->shipmentInfoTest($connector,'100000005');
        $this->carrierTest($connector,$this->_order,'100000005');
       #$this->addCommentTest($connector,'100000005');
       #$serviceResult = $connector->call('sales_order_shipment.create',$data);
       #$this->_createShipmentId = $serviceResult;
       #$this->assertType('string', $serviceResult);
    }
    
    /**
    * @dataProvider connectorProvider
    */
    public function shipmentInfoTest($connector,$shipmentId)
    {
         $serviceResult = $connector->call('sales_order_shipment.info',$shipmentId);
         $this->assertType('array', $serviceResult);   
    }
    
    /**
    * @dataProvider connectorProvider
    */
    public function addCommentTest($connector,$shipmentId)
    {
        $serviceResult = $connector->call('sales_order_shipment.info',$shipmentId);
        $comments = count($serviceResult['comments']);
        $comments++;
        $data = array($shipmentId,'New comment',false,false);
        
        $serviceResult = $connector->call('sales_order_shipment.addComment',$data);
        
        $this->assertType('bool',$serviceResult);
        
        $serviceResult = $connector->call('sales_order_shipment.info',$shipmentId);
        
        $this->assertEquals(count($serviceResult['comments']),$comments);
    }
    
    /**
    * @dataProvider connectorProvider
    */
    public function carrierTest(WebService_Connector_Interface $connector,$orderIncId,$shipmentIncId)
    {
        $serviceResult = $connector->call('sales_order_shipment.getCarriers',$orderIncId);
        $this->assertType('array',$serviceResult);
        
        foreach ($serviceResult as $code=>$title)
        {
            $data = array($shipmentIncId,$code,$code . '-title',rand(1,100));
            $result = $connector->call('sales_order_shipment.addTrack',$data);
             $this->assertTrue((int)$result>0);
        }
        
        $result = $connector->call('sales_order_shipment.info',$shipmentIncId);
        
        $this->assertTrue((int) count($result['tracks'])>0);
        
        foreach ($result['tracks'] as $track)
        {
            $result = $connector->call('sales_order_shipment.removeTrack',array($shipmentIncId,$track['track_id']));
            $this->assertType('bool',$result);
        }
        
        $result = $connector->call('sales_order_shipment.info',$shipmentIncId);
        
        $this->assertTrue(count($result['tracks'])==0);
        
    }
    
   
    
}
