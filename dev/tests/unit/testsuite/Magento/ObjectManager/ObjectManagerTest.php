<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
require __DIR__ . '/../_files/Interface.php';
require __DIR__ . '/../_files/Parent.php';
require __DIR__ . '/../_files/Child.php';
require __DIR__ . '/../_files/Child/A.php';
require __DIR__ . '/../_files/Child/Circular.php';
require __DIR__ . '/../_files/Aggregate/Interface.php';
require __DIR__ . '/../_files/Aggregate/Parent.php';
require __DIR__ . '/../_files/Aggregate/Child.php';
require __DIR__ . '/../_files/Aggregate/WithOptional.php';

class Magento_ObjectManager_ObjectManagerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_ObjectManager_ObjectManager
     */
    protected $_object;

    protected function setUp()
    {
        $definitions = new Magento_ObjectManager_Definition_Runtime();
        $this->_object = new Magento_ObjectManager_ObjectManager(
            $definitions
        );
    }

    public function testCreateCreatesNewInstanceEveryTime()
    {
        $object1 = $this->_object->create('Magento_Test_Di_Child');
        $this->assertInstanceOf('Magento_Test_Di_Child', $object1);
        $object2 = $this->_object->create('Magento_Test_Di_Child');
        $this->assertInstanceOf('Magento_Test_Di_Child', $object2);
        $this->assertNotSame($object1, $object2);
    }

    public function testGetCreatesNewInstanceOnlyOnce()
    {
        $object1 = $this->_object->get('Magento_Test_Di_Child');
        $this->assertInstanceOf('Magento_Test_Di_Child', $object1);
        $object2 = $this->_object->get('Magento_Test_Di_Child');
        $this->assertInstanceOf('Magento_Test_Di_Child', $object2);
        $this->assertSame($object1, $object2);
    }

    public function testCreateCreatesPreferredImplementation()
    {
        $this->_object->configure(array(
            'preferences' => array(
                'Magento_Test_Di_Interface' => 'Magento_Test_Di_Parent',
                'Magento_Test_Di_Parent' => 'Magento_Test_Di_Child'
            )
        ));
        $interface = $this->_object->create('Magento_Test_Di_Interface');
        $parent = $this->_object->create('Magento_Test_Di_Parent');
        $child = $this->_object->create('Magento_Test_Di_Child');
        $this->assertInstanceOf('Magento_Test_Di_Child', $interface);
        $this->assertInstanceOf('Magento_Test_Di_Child', $parent);
        $this->assertInstanceOf('Magento_Test_Di_Child', $child);
        $this->assertNotSame($interface, $parent);
        $this->assertNotSame($interface, $child);
    }

    public function testGetCreatesPreferredImplementation()
    {
        $this->_object->configure(array(
            'preferences' => array(
                'Magento_Test_Di_Interface' => 'Magento_Test_Di_Parent',
                'Magento_Test_Di_Parent' => 'Magento_Test_Di_Child'
            )
        ));
        $interface = $this->_object->get('Magento_Test_Di_Interface');
        $parent = $this->_object->get('Magento_Test_Di_Parent');
        $child = $this->_object->get('Magento_Test_Di_Child');
        $this->assertInstanceOf('Magento_Test_Di_Child', $interface);
        $this->assertInstanceOf('Magento_Test_Di_Child', $parent);
        $this->assertInstanceOf('Magento_Test_Di_Child', $child);
        $this->assertSame($interface, $parent);
        $this->assertSame($interface, $child);
    }

    /**
     * @expectedException BadMethodCallException
     * @expectedExceptionMessage Missing required argument $scalar for Magento_Test_Di_Aggregate_Parent
     */
    public function testCreateThrowsExceptionIfRequiredConstructorParameterIsNotProvided()
    {
        $this->_object->configure(array(
            'preferences' => array(
                'Magento_Test_Di_Interface' => 'Magento_Test_Di_Parent',
                'Magento_Test_Di_Parent' => 'Magento_Test_Di_Child'
            )
        ));
        $this->_object->create('Magento_Test_Di_Aggregate_Parent');
    }

    public function testCreateResolvesScalarParametersAutomatically()
    {
        $this->_object->configure(array(
            'preferences' => array(
                'Magento_Test_Di_Interface' => 'Magento_Test_Di_Parent',
                'Magento_Test_Di_Parent' => 'Magento_Test_Di_Child'
            ),
            'Magento_Test_Di_Aggregate_Parent' => array(
                'parameters' => array(
                    'child' => 'Magento_Test_Di_Child_A',
                    'scalar' => 'scalarValue'
                )
            )
        ));
        /** @var $result Magento_Test_Di_Aggregate_Parent */
        $result = $this->_object->create('Magento_Test_Di_Aggregate_Parent');
        $this->assertInstanceOf('Magento_Test_Di_Aggregate_Parent', $result);
        $this->assertInstanceOf('Magento_Test_Di_Child', $result->interface);
        $this->assertInstanceOf('Magento_Test_Di_Child', $result->parent);
        $this->assertInstanceOf('Magento_Test_Di_Child_A', $result->child);
        $this->assertEquals('scalarValue', $result->scalar);
        $this->assertEquals('1', $result->optionalScalar);
    }

    public function testCreateResolvesScalarCallTimeParametersAutomatically()
    {
        $this->_object->configure(array(
            'preferences' => array(
                'Magento_Test_Di_Interface' => 'Magento_Test_Di_Parent',
                'Magento_Test_Di_Parent' => 'Magento_Test_Di_Child'
            ),
        ));
        /** @var $result Magento_Test_Di_Aggregate_Child */
        $result = $this->_object->create('Magento_Test_Di_Aggregate_Child', array(
            'child' => 'Magento_Test_Di_Child_A',
            'scalar' => 'scalarValue',
            'secondScalar' => 'secondScalarValue',
            'secondOptionalScalar' => 'secondOptionalValue'
        ));
        $this->_assertCreateResolvesScalarCallTimeParametersAutomatically($result);
    }

    /**
     * Sub-routine for asserting test case of resolving arguments
     *
     * @param Magento_Test_Di_Aggregate_Child $object
     */
    private function _assertCreateResolvesScalarCallTimeParametersAutomatically(Magento_Test_Di_Aggregate_Child $object)
    {
        $this->assertInstanceOf('Magento_Test_Di_Aggregate_Child', $object);
        $this->assertInstanceOf('Magento_Test_Di_Child', $object->interface);
        $this->assertInstanceOf('Magento_Test_Di_Child', $object->parent);
        $this->assertInstanceOf('Magento_Test_Di_Child_A', $object->child);
        $this->assertEquals('scalarValue', $object->scalar);
        $this->assertEquals('secondScalarValue', $object->secondScalar);
        $this->assertEquals('1', $object->optionalScalar);
        $this->assertEquals('secondOptionalValue', $object->secondOptionalScalar);
    }

    public function testCreateResolveNumericParameters()
    {
        $this->_object->configure(array(
            'preferences' => array(
                'Magento_Test_Di_Interface' => 'Magento_Test_Di_Parent',
                'Magento_Test_Di_Parent' => 'Magento_Test_Di_Child'
            ),
        ));
        /** @var $result Magento_Test_Di_Aggregate_Child */
        $result = $this->_object->create('Magento_Test_Di_Aggregate_Child', array(
            2 => 'Magento_Test_Di_Child_A',
            'secondOptionalScalar' => 'secondOptionalValue',
            4 => 'secondScalarValue',
            3 => 'scalarValue',
        ));
        // use exactly same assertions as in previous test case to make sure arguments are not confused
        $this->_assertCreateResolvesScalarCallTimeParametersAutomatically($result);
    }

    public function testGetCreatesSharedInstancesEveryTime()
    {
        $this->_object->configure(array(
            'preferences' => array(
                'Magento_Test_Di_Interface' => 'Magento_Test_Di_Parent',
                'Magento_Test_Di_Parent' => 'Magento_Test_Di_Child'
            ),
            'Magento_Test_Di_Interface' => array(
                'shared' => 0
            ),
            'Magento_Test_Di_Aggregate_Parent' => array(
                'parameters' => array(
                    'scalar' => 'scalarValue'
                )
            )
        ));
        /** @var $result Magento_Test_Di_Aggregate_Parent */
        $result = $this->_object->create('Magento_Test_Di_Aggregate_Parent');
        $this->assertInstanceOf('Magento_Test_Di_Aggregate_Parent', $result);
        $this->assertInstanceOf('Magento_Test_Di_Child', $result->interface);
        $this->assertInstanceOf('Magento_Test_Di_Child', $result->parent);
        $this->assertInstanceOf('Magento_Test_Di_Child', $result->child);
        $this->assertNotSame($result->interface, $result->parent);
        $this->assertNotSame($result->interface, $result->child);
        $this->assertSame($result->parent, $result->child);
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Magento_Test_Di_Aggregate_Parent depends on Magento_Test_Di_Child_Circular
     */
    public function testGetDetectsCircularDependency()
    {
        $this->_object->configure(array(
            'preferences' => array(
                'Magento_Test_Di_Interface' => 'Magento_Test_Di_Parent',
                'Magento_Test_Di_Parent' => 'Magento_Test_Di_Child_Circular'
            ),
        ));
        $this->_object->create('Magento_Test_Di_Aggregate_Parent');
    }

    public function testCreateIgnoresOptionalArguments()
    {
        $instance = $this->_object->create('Magento_Test_Di_Aggregate_WithOptional');
        $this->assertNull($instance->parent);
        $this->assertNull($instance->child);
    }
}
