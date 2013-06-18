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
                    'child' => array('instance' => 'Magento_Test_Di_Child_A'),
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

    /**
     * @param array $arguments
     * @dataProvider createResolvesScalarCallTimeParametersAutomaticallyDataProvider
     */
    public function testCreateResolvesScalarCallTimeParametersAutomatically(array $arguments)
    {
        $this->_object->configure(array(
            'preferences' => array(
                'Magento_Test_Di_Interface' => 'Magento_Test_Di_Parent',
                'Magento_Test_Di_Parent' => 'Magento_Test_Di_Child'
            ),
        ));
        /** @var $result Magento_Test_Di_Aggregate_Child */
        $result = $this->_object->create('Magento_Test_Di_Aggregate_Child', $arguments);
        $this->assertInstanceOf('Magento_Test_Di_Aggregate_Child', $result);
        $this->assertInstanceOf('Magento_Test_Di_Child', $result->interface);
        $this->assertInstanceOf('Magento_Test_Di_Child', $result->parent);
        $this->assertInstanceOf('Magento_Test_Di_Child_A', $result->child);
        $this->assertEquals('scalarValue', $result->scalar);
        $this->assertEquals('secondScalarValue', $result->secondScalar);
        $this->assertEquals('1', $result->optionalScalar);
        $this->assertEquals('secondOptionalValue', $result->secondOptionalScalar);
    }

    public function createResolvesScalarCallTimeParametersAutomaticallyDataProvider()
    {
        return array(
            'named binding' => array(
                array(
                    'child' => array('instance' => 'Magento_Test_Di_Child_A'),
                    'scalar' => 'scalarValue',
                    'secondScalar' => 'secondScalarValue',
                    'secondOptionalScalar' => 'secondOptionalValue'
                )
            )
        );
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

    public function testCreateCreatesPreconfiguredInstance()
    {
        $this->_object->configure(array(
            'preferences' => array(
                'Magento_Test_Di_Interface' => 'Magento_Test_Di_Parent',
                'Magento_Test_Di_Parent' => 'Magento_Test_Di_Child'
            ),
            'customChildType' => array(
                'type' => 'Magento_Test_Di_Aggregate_Child',
                'parameters' => array(
                    'scalar' => 'configuredScalar',
                    'secondScalar' => 'configuredSecondScalar',
                    'secondOptionalScalar' => 'configuredOptionalScalar'
                )
            )
        ));
        $customChild = $this->_object->get('customChildType');
        $this->assertInstanceOf('Magento_Test_Di_Aggregate_Child', $customChild);
        $this->assertEquals('configuredScalar', $customChild->scalar);
        $this->assertEquals('configuredSecondScalar', $customChild->secondScalar);
        $this->assertEquals(1, $customChild->optionalScalar);
        $this->assertEquals('configuredOptionalScalar', $customChild->secondOptionalScalar);
        $this->assertSame($customChild, $this->_object->get('customChildType'));
    }

    public function testParameterShareabilityConfigurationIsApplied()
    {
        $this->_object->configure(array(
            'customChildType' => array(
                'type' => 'Magento_Test_Di_Aggregate_Child',
                'parameters' => array(
                    'interface' => array('instance' => 'Magento_Test_Di_Parent'),
                    'scalar' => 'configuredScalar',
                    'secondScalar' => 'configuredSecondScalar',
                )
            )
        ));
        $child1 = $this->_object->create('customChildType');
        $child2 = $this->_object->create('customChildType');
        $this->assertNotSame($child1, $child2);
        $this->assertSame($child1->interface, $child2->interface);

        $this->_object->configure(array(
            'customChildType' => array(
                'parameters' => array(
                    'interface' => array('instance' => 'Magento_Test_Di_Parent', 'shared' => false),
                )
            )
        ));
        $child1 = $this->_object->create('customChildType');
        $child2 = $this->_object->create('customChildType');
        $this->assertNotSame($child1, $child2);
        $this->assertNotSame($child1->interface, $child2->interface);
    }

    public function testTypeShareabilityConfigurationIsApplied()
    {
        $this->_object->configure(array(
            'customChildType' => array(
                'type' => 'Magento_Test_Di_Aggregate_Child',
                'parameters' => array(
                    'interface' => array('instance' => 'Magento_Test_Di_Parent'),
                    'scalar' => 'configuredScalar',
                    'secondScalar' => 'configuredSecondScalar',
                )
            )
        ));
        $child1 = $this->_object->create('customChildType');
        $child2 = $this->_object->create('customChildType');
        $this->assertNotSame($child1, $child2);
        $this->assertSame($child1->interface, $child2->interface);

        $this->_object->configure(array(
            'Magento_Test_Di_Parent' => array(
                'shared' => 'false'
            )
        ));
        $child1 = $this->_object->create('customChildType');
        $child2 = $this->_object->create('customChildType');
        $this->assertNotSame($child1, $child2);
        $this->assertNotSame($child1->interface, $child2->interface);
    }

    public function testParameterShareabilityConfigurationOverridesTypeShareability()
    {
        $this->_object->configure(array(
            'Magento_Test_Di_Parent' => array(
                'shared' => 'false'
            ),
            'customChildType' => array(
                'type' => 'Magento_Test_Di_Aggregate_Child',
                'parameters' => array(
                    'interface' => array('instance' => 'Magento_Test_Di_Parent'),
                    'scalar' => 'configuredScalar',
                    'secondScalar' => 'configuredSecondScalar',
                )
            )
        ));
        $child1 = $this->_object->create('customChildType');
        $child2 = $this->_object->create('customChildType');
        $this->assertNotSame($child1, $child2);
        $this->assertNotSame($child1->interface, $child2->interface);

        $this->_object->configure(array(
            'customChildType' => array(
                'parameters' => array(
                    'interface' => array('instance' => 'Magento_Test_Di_Parent', 'shared' => 'true'),
                )
            )
        ));
        $child1 = $this->_object->create('customChildType');
        $child2 = $this->_object->create('customChildType');
        $this->assertNotSame($child1, $child2);
        $this->assertSame($child1->interface, $child2->interface);
    }
}
