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
		$this->registry->addService('Product', '1');
		$this->registry->addService('Cart', '1');
		$this->registry->addMethod('getProduct', 'Product', '1', array('products'), 'product.xsd', 'getProductRequest', 'product.xsd', 'getProductResponse');
		$this->registry->addMethod('addProduct', 'Product', '1', array('products'), 'product.xsd', 'addProductRequest', 'product.xsd', 'addProductResponse');
		$this->registry->addMethod('getCart', 'Cart', '1', array('cart'), 'cart.xsd', 'getCartRequest', 'cart.xsd', 'getCartResponse');
	}

    /**
     * @expectedException InvalidArgumentException
     */
	public function testGetServiceNonExists ()
	{
		$this->registry->getService('BogusService', '1');
	}

	public function testGetServiceExists ()
	{
		$s = $this->registry->getService('Product', '1');
		$this->assertTrue($s->getName() == 'Product');
		$this->assertTrue($s->getVersion() == '1');
	}

	public function testAddServiceNew ()
	{
		$s = $this->registry->addService('NewService', '2');
		$this->assertTrue($s->getName() == 'NewService');
		$this->assertTrue($s->getVersion() == '2');
	}

	public function testAddServiceExists ()
	{
		$s = $this->registry->addService('Product', '1');
		$this->assertTrue($s->getName() == 'Product');
		$this->assertTrue($s->getVersion() == '1');
	}

	public function testAddServiceNewVersion ()
	{
		$s = $this->registry->addService('Product', '2');
		$this->assertTrue($s->getName() == 'Product');
		$this->assertTrue($s->getVersion() == '2');
	}

    /**
     * @expectedException InvalidArgumentException
     */
	public function testGetMethodNonExistService ()
	{
		$m = $this->registry->getMethod('newMethod', 'NewService', '1');
	}

    /**
     * @expectedException InvalidArgumentException
     */
	public function testGetMethodNonExistMethod ()
	{
		$m = $this->registry->getMethod('newMethod', 'Product', '1');
	}

	public function testGetMethodexists ()
	{
		$m = $this->registry->getMethod('getProduct', 'Product', '1');
		$this->assertTrue($m->getName() == 'getProduct');
	}

    /**
     * @expectedException InvalidArgumentException
     */
	public function testAddMethodToNewService ()
	{
		$m = $this->registry->addMethod('newMethod', 'newService', '1', array('p1', 'p2'), 'schema.xsd', 'requestElement', 'schema.xsd', 'responseElement');
	}

	public function testAddMethodToExistService ()
	{
		$m = $this->registry->addMethod('newMethod', 'Product', '1', array('p1', 'p2'), 'schema.xsd', 'requestElement', 'schema.xsd', 'responseElement');
		$this->assertTrue($m->getName() == 'newMethod');
		$this->assertTrue($m->getInputSchema() == 'schema.xsd');
	}
}