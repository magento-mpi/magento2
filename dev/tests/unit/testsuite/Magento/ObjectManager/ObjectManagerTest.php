<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\ObjectManager;

require __DIR__ . '/../_files/ChildInterface.php';
require __DIR__ . '/../_files/DiParent.php';
require __DIR__ . '/../_files/Child.php';
require __DIR__ . '/../_files/Child/A.php';
require __DIR__ . '/../_files/Child/Circular.php';
require __DIR__ . '/../_files/Aggregate/AggregateInterface.php';
require __DIR__ . '/../_files/Aggregate/AggregateParent.php';
require __DIR__ . '/../_files/Aggregate/Child.php';
require __DIR__ . '/../_files/Aggregate/WithOptional.php';

class ObjectManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\ObjectManager\ObjectManager
     */
    protected $_object;

    protected function setUp()
    {
        $config = new \Magento\ObjectManager\Config\Config(new \Magento\ObjectManager\Relations\Runtime());
        $factory = new \Magento\ObjectManager\Factory\Factory(
                $config, null, null, array('one' => 'first_val', 'two' => 'second_val')
        );
        $this->_object = new \Magento\ObjectManager\ObjectManager($factory, $config);
    }

    public function testCreateCreatesNewInstanceEveryTime()
    {
        $objectA = $this->_object->create('Magento\Test\Di\Child');
        $this->assertInstanceOf('Magento\Test\Di\Child', $objectA);
        $objectB = $this->_object->create('Magento\Test\Di\Child');
        $this->assertInstanceOf('Magento\Test\Di\Child', $objectB);
        $this->assertNotSame($objectA, $objectB);
    }

    public function testGetCreatesNewInstanceOnlyOnce()
    {
        $objectA = $this->_object->get('Magento\Test\Di\Child');
        $this->assertInstanceOf('Magento\Test\Di\Child', $objectA);
        $objectB = $this->_object->get('Magento\Test\Di\Child');
        $this->assertInstanceOf('Magento\Test\Di\Child', $objectB);
        $this->assertSame($objectA, $objectB);
    }

    public function testCreateCreatesPreferredImplementation()
    {
        $this->_object->configure(array(
            'preferences' => array(
                'Magento\Test\Di\DiInterface' => 'Magento\Test\Di\DiParent',
                'Magento\Test\Di\DiParent' => 'Magento\Test\Di\Child'
            )
        ));
        $interface = $this->_object->create('Magento\Test\Di\DiInterface');
        $parent = $this->_object->create('Magento\Test\Di\DiParent');
        $child = $this->_object->create('Magento\Test\Di\Child');
        $this->assertInstanceOf('Magento\Test\Di\Child', $interface);
        $this->assertInstanceOf('Magento\Test\Di\Child', $parent);
        $this->assertInstanceOf('Magento\Test\Di\Child', $child);
        $this->assertNotSame($interface, $parent);
        $this->assertNotSame($interface, $child);
    }

    public function testGetCreatesPreferredImplementation()
    {
        $this->_object->configure(array(
            'preferences' => array(
                'Magento\Test\Di\DiInterface' => 'Magento\Test\Di\DiParent',
                'Magento\Test\Di\DiParent' => 'Magento\Test\Di\Child'
            )
        ));
        $interface = $this->_object->get('Magento\Test\Di\DiInterface');
        $parent = $this->_object->get('Magento\Test\Di\DiParent');
        $child = $this->_object->get('Magento\Test\Di\Child');
        $this->assertInstanceOf('Magento\Test\Di\Child', $interface);
        $this->assertInstanceOf('Magento\Test\Di\Child', $parent);
        $this->assertInstanceOf('Magento\Test\Di\Child', $child);
        $this->assertSame($interface, $parent);
        $this->assertSame($interface, $child);
    }

    /**
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessage Missing required argument $scalar for Magento\Test\Di\Aggregate\AggregateParent
     */
    public function testCreateThrowsExceptionIfRequiredConstructorParameterIsNotProvided()
    {
        $this->_object->configure(array(
            'preferences' => array(
                'Magento\Test\Di\DiInterface' => 'Magento\Test\Di\DiParent',
                'Magento\Test\Di\DiParent' => 'Magento\Test\Di\Child'
            )
        ));
        $this->_object->create('Magento\Test\Di\Aggregate\AggregateParent');
    }

    public function testCreateResolvesScalarParametersAutomatically()
    {
        $this->_object->configure(array(
            'preferences' => array(
                'Magento\Test\Di\DiInterface' => 'Magento\Test\Di\DiParent',
                'Magento\Test\Di\DiParent' => 'Magento\Test\Di\Child'
            ),
            'Magento\Test\Di\Aggregate\AggregateParent' => array(
                'arguments' => array(
                    'child' => array('xsi:type' => 'object', 'value' => 'Magento\Test\Di\Child\A'),
                    'scalar' => array('xsi:type' => 'string', 'value' => 'scalarValue')
                )
            )
        ));
        /** @var $result \Magento\Test\Di\Aggregate\AggregateParent */
        $result = $this->_object->create('Magento\Test\Di\Aggregate\AggregateParent');
        $this->assertInstanceOf('Magento\Test\Di\Aggregate\AggregateParent', $result);
        $this->assertInstanceOf('Magento\Test\Di\Child', $result->interface);
        $this->assertInstanceOf('Magento\Test\Di\Child', $result->parent);
        $this->assertInstanceOf('Magento\Test\Di\Child\A', $result->child);
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
                'Magento\Test\Di\DiInterface' => 'Magento\Test\Di\DiParent',
                'Magento\Test\Di\DiParent' => 'Magento\Test\Di\Child'
            ),
        ));
        /** @var $result \Magento\Test\Di\Aggregate\Child */
        $result = $this->_object->create('Magento\Test\Di\Aggregate\Child', $arguments);
        $this->assertInstanceOf('Magento\Test\Di\Aggregate\Child', $result);
        $this->assertInstanceOf('Magento\Test\Di\Child', $result->interface);
        $this->assertInstanceOf('Magento\Test\Di\Child', $result->parent);
        $this->assertInstanceOf('Magento\Test\Di\Child\A', $result->child);
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
                    'child' => array('instance' => 'Magento\Test\Di\Child\A'),
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
                'Magento\Test\Di\DiInterface' => 'Magento\Test\Di\DiParent',
                'Magento\Test\Di\DiParent' => 'Magento\Test\Di\Child'
            ),
            'Magento\Test\Di\DiInterface' => array(
                'shared' => 0
            ),
            'Magento\Test\Di\Aggregate\AggregateParent' => array(
                'arguments' => array(
                    'scalar' => array('xsi:type' => 'string', 'value' => 'scalarValue')
                )
            )
        ));
        /** @var $result \Magento\Test\Di\Aggregate\AggregateParent */
        $result = $this->_object->create('Magento\Test\Di\Aggregate\AggregateParent');
        $this->assertInstanceOf('Magento\Test\Di\Aggregate\AggregateParent', $result);
        $this->assertInstanceOf('Magento\Test\Di\Child', $result->interface);
        $this->assertInstanceOf('Magento\Test\Di\Child', $result->parent);
        $this->assertInstanceOf('Magento\Test\Di\Child', $result->child);
        $this->assertNotSame($result->interface, $result->parent);
        $this->assertNotSame($result->interface, $result->child);
        $this->assertSame($result->parent, $result->child);
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Magento\Test\Di\Aggregate\AggregateParent depends on Magento\Test\Di\Child\Circular
     */
    public function testGetDetectsCircularDependency()
    {
        $this->_object->configure(array(
            'preferences' => array(
                'Magento\Test\Di\DiInterface' => 'Magento\Test\Di\DiParent',
                'Magento\Test\Di\DiParent' => 'Magento\Test\Di\Child\Circular'
            ),
        ));
        $this->_object->create('Magento\Test\Di\Aggregate\AggregateParent');
    }

    public function testCreateIgnoresOptionalArguments()
    {
        $instance = $this->_object->create('Magento\Test\Di\Aggregate\WithOptional');
        $this->assertNull($instance->parent);
        $this->assertNull($instance->child);
    }

    public function testCreateInstantiatesOptionalObjectArgumentsIfTheyreProvided()
    {
        $instance = $this->_object->create(
            'Magento\Test\Di\Aggregate\WithOptional', array('child' => array('instance' => 'Magento\Test\Di\Child'))
        );
        $this->assertNull($instance->parent);
        $this->assertInstanceOf('Magento\Test\Di\Child', $instance->child);
    }

    public function testCreateCreatesPreconfiguredInstance()
    {
        $this->_object->configure(array(
            'preferences' => array(
                'Magento\Test\Di\DiInterface' => 'Magento\Test\Di\DiParent',
                'Magento\Test\Di\DiParent' => 'Magento\Test\Di\Child'
            ),
            'customChildType' => array(
                'type' => 'Magento\Test\Di\Aggregate\Child',
                'arguments' => array(
                    'scalar' => array('xsi:type' => 'configuredScalar'),
                    'secondScalar' => array('xsi:type' => 'string', 'value' => 'configuredSecondScalar'),
                    'secondOptionalScalar' => array('xsi:type' => 'string', 'value' => 'configuredOptionalScalar')
                )
            )
        ));
        $customChild = $this->_object->get('customChildType');
        $this->assertInstanceOf('Magento\Test\Di\Aggregate\Child', $customChild);
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
                'type' => 'Magento\Test\Di\Aggregate\Child',
                'arguments' => array(
                    'interface' => array('xsi:type' => 'object', 'value' => 'Magento\Test\Di\DiParent'),
                    'scalar' => array('xsi:type' => 'string', 'value' => 'configuredScalar'),
                    'secondScalar' => array('xsi:type' => 'string', 'value' => 'configuredSecondScalar'),
                )
            )
        ));
        $childA = $this->_object->create('customChildType');
        $childB = $this->_object->create('customChildType');
        $this->assertNotSame($childA, $childB);
        $this->assertSame($childA->interface, $childB->interface);

        $this->_object->configure(array(
            'customChildType' => array(
                'arguments' => array(
                    'interface'
                        => array('xsi:type' => 'object', 'value' => 'Magento\Test\Di\DiParent', 'shared' => false),
                )
            )
        ));
        $childA = $this->_object->create('customChildType');
        $childB = $this->_object->create('customChildType');
        $this->assertNotSame($childA, $childB);
        $this->assertNotSame($childA->interface, $childB->interface);
    }

    public function testTypeShareabilityConfigurationIsApplied()
    {
        $this->_object->configure(array(
            'customChildType' => array(
                'type' => 'Magento\Test\Di\Aggregate\Child',
                'arguments' => array(
                    'interface' => array('xsi:type' => 'object', 'value' => 'Magento\Test\Di\DiParent'),
                    'scalar' => array('xsi:type' => 'string', 'value' => 'configuredScalar'),
                    'secondScalar' => array('xsi:type' => 'string', 'value' => 'configuredSecondScalar'),
                )
            )
        ));
        $childA = $this->_object->create('customChildType');
        $childB = $this->_object->create('customChildType');
        $this->assertNotSame($childA, $childB);
        $this->assertSame($childA->interface, $childB->interface);

        $this->_object->configure(array(
            'Magento\Test\Di\DiParent' => array(
                'shared' => false
            )
        ));
        $childA = $this->_object->create('customChildType');
        $childB = $this->_object->create('customChildType');
        $this->assertNotSame($childA, $childB);
        $this->assertNotSame($childA->interface, $childB->interface);
    }

    public function testParameterShareabilityConfigurationOverridesTypeShareability()
    {
        $this->_object->configure(array(
            'Magento\Test\Di\DiParent' => array(
                'shared' => false
            ),
            'customChildType' => array(
                'type' => 'Magento\Test\Di\Aggregate\Child',
                'arguments' => array(
                    'interface' => array('xsi:type' => 'object', 'value' => 'Magento\Test\Di\DiParent'),
                    'scalar' => array('xsi:type' => 'string', 'value' => 'configuredScalar'),
                    'secondScalar' => array('xsi:type' => 'string', 'value' => 'configuredSecondScalar'),
                )
            )
        ));
        $childA = $this->_object->create('customChildType');
        $childB = $this->_object->create('customChildType');
        $this->assertNotSame($childA, $childB);
        $this->assertNotSame($childA->interface, $childB->interface);

        $this->_object->configure(array(
            'customChildType' => array(
                'arguments' => array(
                    'interface'
                        => array('xsi:type' => 'object', 'value' => 'Magento\Test\Di\DiParent', 'shared' => true),
                )
            )
        ));
        $childA = $this->_object->create('customChildType');
        $childB = $this->_object->create('customChildType');
        $this->assertNotSame($childA, $childB);
        $this->assertSame($childA->interface, $childB->interface);
    }

    public function testGlobalArgumentsCanBeConfigured()
    {
        $this->_object->configure(array(
            'preferences' => array(
                'Magento\Test\Di\DiInterface' => 'Magento\Test\Di\DiParent',
            ),
            'Magento\Test\Di\Aggregate\AggregateParent' => array(
                'arguments' => array(
                    'scalar' => array('xsi:type' => 'init_parameter', 'value' => 'one'),
                    'optionalScalar' => array('xsi:type' => 'init_parameter', 'value' => 'two')
                )
            )
        ));
        /** @var $result \Magento\Test\Di\Aggregate\AggregateParent */
        $result = $this->_object->create('Magento\Test\Di\Aggregate\AggregateParent');
        $this->assertEquals('first_val', $result->scalar);
        $this->assertEquals('second_val', $result->optionalScalar);
    }

    public function testConfiguredArgumentsAreInherited()
    {
        $this->_object->configure(array(
            'Magento\Test\Di\Aggregate\AggregateParent' => array(
                'arguments' => array(
                    'interface' => array('xsi:type' => 'object', 'value' => 'Magento\Test\Di\DiParent'),
                    'scalar' => array('xsi:type' => 'init_parameter', 'value' => 'one'),
                    'optionalScalar' => array('xsi:type' => 'string', 'value' => 'parentOptionalScalar')
                )
            ),
            'Magento\Test\Di\Aggregate\Child' => array(
                'arguments' => array(
                    'secondScalar' => array('xsi:type' => 'string', 'value' => 'childSecondScalar'),
                )
            )
        ));

        /** @var $result \Magento\Test\Di\Aggregate\AggregateParent */
        $result = $this->_object->create('Magento\Test\Di\Aggregate\Child');
        $this->assertInstanceOf('Magento\Test\Di\DiParent', $result->interface);
        $this->assertEquals('first_val', $result->scalar);
        $this->assertEquals('childSecondScalar', $result->secondScalar);
        $this->assertEquals('parentOptionalScalar', $result->optionalScalar);
    }

    public function testConfiguredArgumentsOverrideInheritedArguments()
    {
        $this->_object->configure(array(
            'Magento\Test\Di\Aggregate\AggregateParent' => array(
                'arguments' => array(
                    'interface' => array('xsi:type' => 'object', 'value' => 'Magento\Test\Di\DiParent'),
                    'scalar' => array('xsi:type' => 'init_parameter', 'value' => 'one'),
                    'optionalScalar' => array('xsi:type' => 'string', 'value' => 'parentOptionalScalar')
                )
            ),
            'Magento\Test\Di\Aggregate\Child' => array(
                'arguments' => array(
                    'interface' => array('xsi:type' => 'object', 'value' => 'Magento\Test\Di\Child'),
                    'scalar' => array('xsi:type' => 'init_parameter', 'value' => 'two'),
                    'secondScalar' => array('xsi:type' => 'string', 'value' => 'childSecondScalar'),
                    'optionalScalar' => array('xsi:type' => 'string', 'value' => 'childOptionalScalar')
                )
            )
        ));

        /** @var $result \Magento\Test\Di\Aggregate\AggregateParent */
        $result = $this->_object->create('Magento\Test\Di\Aggregate\Child');
        $this->assertInstanceOf('Magento\Test\Di\Child', $result->interface);
        $this->assertEquals('second_val', $result->scalar);
        $this->assertEquals('childSecondScalar', $result->secondScalar);
        $this->assertEquals('childOptionalScalar', $result->optionalScalar);
    }
}
