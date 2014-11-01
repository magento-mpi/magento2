<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Service\Entity;

use Magento\Framework\Api\AbstractExtensibleObject;
use Magento\Framework\Api\AbstractExtensibleObjectTest;
use Magento\Webapi\Controller\ServiceArgsSerializer;

class DataFromArrayTest extends \PHPUnit_Framework_TestCase
{
    /** @var ServiceArgsSerializer */
    protected $serializer;

    public function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $objectFactory = new \Magento\Webapi\Service\Entity\WebapiObjectManager($objectManager);
        $typeProcessor = $objectManager->getObject('Magento\Webapi\Model\Config\ClassReflector\TypeProcessor');
        $this->serializer = $objectManager->getObject(
            'Magento\Webapi\Controller\ServiceArgsSerializer',
            ['typeProcessor' => $typeProcessor, 'objectManager' => $objectFactory]
        );
    }

    public function testSimpleProperties()
    {
        $data = array('entityId' => 15, 'name' => 'Test');
        $result = $this->serializer->getInputData('\\Magento\\Webapi\\Service\\Entity\\TestService', 'simple', $data);
        $this->assertNotNull($result);
        $this->assertEquals(15, $result[0]);
        $this->assertEquals('Test', $result[1]);
    }

    public function testNestedDataProperties()
    {
        $data = array('nested' => array('details' => array('entityId' => 15, 'name' => 'Test')));
        $result = $this->serializer->getInputData(
            '\\Magento\\Webapi\\Service\\Entity\\TestService',
            'nestedData',
            $data
        );
        $this->assertNotNull($result);
        $this->assertTrue($result[0] instanceof Nested);
        /** @var array $result */
        $this->assertEquals(1, count($result));
        $this->assertNotEmpty($result[0]);
        /** @var NestedData $arg */
        $arg = $result[0];
        $this->assertTrue($arg instanceof Nested);
        /** @var SimpleData $details */
        $details = $arg->getDetails();
        $this->assertNotNull($details);
        $this->assertTrue($details instanceof Simple);
        $this->assertEquals(15, $details->getEntityId());
        $this->assertEquals('Test', $details->getName());
    }

    public function testSimpleArrayProperties()
    {
        $data = array('ids' => array(1, 2, 3, 4));
        $result = $this->serializer->getInputData(
            '\\Magento\\Webapi\\Service\\Entity\\TestService',
            'simpleArray',
            $data
        );
        $this->assertNotNull($result);
        /** @var array $result */
        $this->assertEquals(1, count($result));
        /** @var array $ids */
        $ids = $result[0];
        $this->assertNotNull($ids);
        $this->assertEquals(4, count($ids));
        $this->assertEquals($data['ids'], $ids);
    }

    public function testAssociativeArrayProperties()
    {
        $data = array('associativeArray' => array('key' => 'value', 'key_two' => 'value_two'));
        $result = $this->serializer->getInputData(
            '\\Magento\\Webapi\\Service\\Entity\\TestService',
            'associativeArray',
            $data
        );
        $this->assertNotNull($result);
        /** @var array $result */
        $this->assertEquals(1, count($result));
        /** @var array $associativeArray */
        $associativeArray = $result[0];
        $this->assertNotNull($associativeArray);
        $this->assertEquals('value', $associativeArray['key']);
        $this->assertEquals('value_two', $associativeArray['key_two']);
    }

    public function testArrayOfDataObjectProperties()
    {
        $data = array(
            'dataObjects' => array(
                array('entityId' => 14, 'name' => 'First'),
                array('entityId' => 15, 'name' => 'Second')
            )
        );
        $result = $this->serializer->getInputData(
            '\\Magento\\Webapi\\Service\\Entity\\TestService',
            'dataArray',
            $data
        );
        $this->assertNotNull($result);
        /** @var array $result */
        $this->assertEquals(1, count($result));
        /** @var array $dataObjects */
        $dataObjects = $result[0];
        $this->assertEquals(2, count($dataObjects));
        /** @var SimpleData $first */
        $first = $dataObjects[0];
        /** @var SimpleData $second */
        $second = $dataObjects[1];
        $this->assertTrue($first instanceof Simple);
        $this->assertEquals(14, $first->getEntityId());
        $this->assertEquals('First', $first->getName());
        $this->assertTrue($second instanceof Simple);
        $this->assertEquals(15, $second->getEntityId());
        $this->assertEquals('Second', $second->getName());
    }

    public function testNestedSimpleArrayProperties()
    {
        $data = array('arrayData' => array('ids' => array(1, 2, 3, 4)));
        $result = $this->serializer->getInputData(
            '\\Magento\\Webapi\\Service\\Entity\\TestService',
            'nestedSimpleArray',
            $data
        );
        $this->assertNotNull($result);
        /** @var array $result */
        $this->assertEquals(1, count($result));
        /** @var SimpleArrayData $dataObject */
        $dataObject = $result[0];
        $this->assertTrue($dataObject instanceof SimpleArray);
        /** @var array $ids */
        $ids = $dataObject->getIds();
        $this->assertNotNull($ids);
        $this->assertEquals(4, count($ids));
        $this->assertEquals($data['arrayData']['ids'], $ids);
    }

    public function testNestedAssociativeArrayProperties()
    {
        $data = array(
            'associativeArrayData' => array('associativeArray' => array('key' => 'value', 'key2' => 'value2'))
        );
        $result = $this->serializer->getInputData(
            '\\Magento\\Webapi\\Service\\Entity\\TestService',
            'nestedAssociativeArray',
            $data
        );
        $this->assertNotNull($result);
        /** @var array $result */
        $this->assertEquals(1, count($result));
        /** @var AssociativeArrayData $dataObject */
        $dataObject = $result[0];
        $this->assertTrue($dataObject instanceof AssociativeArray);
        /** @var array $associativeArray */
        $associativeArray = $dataObject->getAssociativeArray();
        $this->assertNotNull($associativeArray);
        $this->assertEquals('value', $associativeArray['key']);
        $this->assertEquals('value2', $associativeArray['key2']);
    }

    public function testNestedArrayOfDataObjectProperties()
    {
        $data = array(
            'dataObjects' => array(
                'items' => array(array('entityId' => 1, 'name' => 'First'), array('entityId' => 2, 'name' => 'Second'))
            )
        );
        $result = $this->serializer->getInputData(
            '\\Magento\\Webapi\\Service\\Entity\\TestService',
            'nestedDataArray',
            $data
        );
        $this->assertNotNull($result);
        /** @var array $result */
        $this->assertEquals(1, count($result));
        /** @var DataArrayData $dataObjects */
        $dataObjects = $result[0];
        $this->assertTrue($dataObjects instanceof DataArray);
        /** @var array $items */
        $items = $dataObjects->getItems();
        $this->assertEquals(2, count($items));
        /** @var SimpleData $first */
        $first = $items[0];
        /** @var SimpleData $second */
        $second = $items[1];
        $this->assertTrue($first instanceof Simple);
        $this->assertEquals(1, $first->getEntityId());
        $this->assertEquals('First', $first->getName());
        $this->assertTrue($second instanceof Simple);
        $this->assertEquals(2, $second->getEntityId());
        $this->assertEquals('Second', $second->getName());
    }
}
