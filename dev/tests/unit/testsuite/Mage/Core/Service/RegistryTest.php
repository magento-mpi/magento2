<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Service_RegistryTest extends PHPUnit_Framework_TestCase
{

    protected function setUp()
	{
		$this->registry = Mage_Core_Service_Registry::getInstance();
		$this->registry->addService('Product', '1', 'Catalog');
		$this->registry->addService('Cart', '1', 'Checkout');
		$this->registry->addMethod('getProduct', 'Product', '1', array('products'));
		$this->registry->addMethod('addProduct', 'Product', '1', array('products'));
		$this->registry->addMethod('getCart', 'Cart', '1', array('cart'));
	}

    /**
     * @expectedException InvalidArgumentException
     */
	public function testGetNonExistingService ()
	{
		$this->registry->getService('BogusService', '1');
	}

	public function testGetExistingService ()
	{
		$s = $this->registry->getService('Product', '1');
		$this->assertTrue($s['name'] == 'Product');
		$this->assertTrue($s['version'] == '1');
	}

	public function testAddNewService ()
	{
		$s = $this->registry->addService('NewService', '2', 'NewModule');
		$this->assertTrue($s['name'] == 'NewService');
		$this->assertTrue($s['version'] == '2');
	}

	public function testAddExistingService ()
	{
		$s = $this->registry->addService('Product', '1', 'Catalog');
		$this->assertTrue($s['name'] == 'Product');
		$this->assertTrue($s['version'] == '1');
	}

	public function testAddNewVersionOfExistingService ()
	{
		$s = $this->registry->addService('Product', '2', 'Catalog');
		$this->assertTrue($s['name'] == 'Product');
		$this->assertTrue($s['version'] == '2');
	}

    /**
     * @expectedException InvalidArgumentException
     */
	public function testGetMethodOfNonExistingService ()
	{
		$m = $this->registry->getMethod('newMethod', 'NewService', '1');
	}

    /**
     * @expectedException InvalidArgumentException
     */
	public function testGetNonExistingMethod ()
	{
		$m = $this->registry->getMethod('newMethod', 'Product', '1');
	}

	public function testGetExistingMethod ()
	{
		$m = $this->registry->getMethod('getProduct', 'Product', '1');
		$this->assertTrue($m['name'] == 'getProduct');
	}

    /**
     * @expectedException InvalidArgumentException
     */
	public function testAddMethodToNonExistingService ()
	{
		$m = $this->registry->addMethod('newMethod', 'newService', '1', array('p1', 'p2'));
	}

	public function testAddMethodToExistingService ()
	{
		$m = $this->registry->addMethod('newMethod', 'Product', '1', array('p1', 'p2'));
		$this->assertTrue($m['name'] == 'newMethod');
	}

    /**
     * @expectedException InvalidArgumentException
     */
	public function testInstantiateNonExistingService ()
	{
		$obj = $this->registry->instantiateService('SuperBogusService', '1');
	}

	public function testInstantiateExistingService ()
	{
		$obj = $this->registry->instantiateService('Product', '1');
		$this->assertTrue('Mage_Catalog_Service_Product' == get_class($obj));
	}

}