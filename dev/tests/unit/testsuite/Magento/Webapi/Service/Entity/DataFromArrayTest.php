<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webapi\Service\Entity;

use Magento\Service\Data\AbstractObject;
use Magento\Service\Data\AbstractObjectTest;
use Magento\Webapi\Controller\ServiceArgsSerializer;

class DataFromArrayTest extends \PHPUnit_Framework_TestCase
{
    /** @var ServiceArgsSerializer */
    protected $serializer;

    public function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $typeProcessor = $objectManager->getObject('Magento\Webapi\Model\Config\ClassReflector\TypeProcessor');
        $this->serializer = new ServiceArgsSerializer($typeProcessor);
    }

    public function testSimpleProperties()
    {
        $data = ['entityId' => 15, 'name' => 'Test'];
        $result = $this->serializer
                       ->getInputData('\\Magento\\Webapi\\Service\\Entity\\TestService', 'simple', $data);
        $this->assertNotNull($result);
        $this->assertEquals(15, $result[0]);
        $this->assertEquals('Test', $result[1]);
    }

    public function testNestedDataProperties()
    {
        $data = ['nested' => ['details' => ['entityId' => 15, 'name' => 'Test']]];
        $result = $this->serializer
            ->getInputData('\\Magento\\Webapi\\Service\\Entity\\TestService', 'nestedData', $data);
        $this->assertNotNull($result);
        $this->assertTrue( $result[0] instanceof NestedData);
        /** @var array $result */
        $this->assertEquals(1, count($result));
        $this->assertNotEmpty($result[0]);
        /** @var NestedData $arg */
        $arg = $result[0];
        $this->assertTrue($arg instanceof NestedData);
        /** @var SimpleData $details */
        $details = $arg->getDetails();
        $this->assertNotNull($details);
        $this->assertTrue($details instanceof SimpleData);
        $this->assertEquals(15, $details->getEntityId());
        $this->assertEquals('Test', $details->getName());
    }

    public function testSimpleArrayProperties()
    {
        $data = ['ids'=>[1,2,3,4]];
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
        $data = ['associativeArray' => ['key' => 'value', 'key_two' => 'value_two']];
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

    public function testArrayOfDtoProperties()
    {
        $data = ['dataObjects' => [ ['entityId' => 14, 'name' => 'First'], [ 'entityId' => 15, 'name' => 'Second' ] ]];
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
        $this->assertTrue($first instanceof SimpleData);
        $this->assertEquals(14, $first->getEntityId());
        $this->assertEquals('First', $first->getName());
        $this->assertTrue($second instanceof SimpleData);
        $this->assertEquals(15, $second->getEntityId());
        $this->assertEquals('Second', $second->getName());
    }

    public function testNestedSimpleArrayProperties()
    {
        $data = ['arrayData' => ['ids' => [1, 2, 3, 4]]];
        $result = $this->serializer
            ->getInputData('\\Magento\\Webapi\\Service\\Entity\\TestService', 'nestedSimpleArray', $data);
        $this->assertNotNull($result);
        /** @var array $result */
        $this->assertEquals(1, count($result));
        /** @var SimpleArrayData $dataObject */
        $dataObject = $result[0];
        $this->assertTrue($dataObject instanceof SimpleArrayData);
        /** @var array $ids */
        $ids = $dataObject->getIds();
        $this->assertNotNull($ids);
        $this->assertEquals(4, count($ids));
        $this->assertEquals($data['arrayData']['ids'], $ids);
    }

    public function testNestedAssociativeArrayProperties()
    {
        $data = ['associativeArrayData' => ['associativeArray' => ['key' => 'value', 'key2' => 'value2']]];
        $result = $this->serializer->getInputData(
            '\\Magento\\Webapi\\Service\\Entity\\TestService',
            'nestedAssociativeArray',
            $data
        );
        $this->assertNotNull($result);
        /** @var array $result */
        $this->assertEquals(1, count($result));
        /** @var AssociativeArrayDto $dataObject */
        $dataObject = $result[0];
        $this->assertTrue($dataObject instanceof AssociativeArrayData);
        /** @var array $associativeArray */
        $associativeArray = $dataObject->getAssociativeArray();
        $this->assertNotNull($associativeArray);
        $this->assertEquals('value', $associativeArray['key']);
        $this->assertEquals('value2', $associativeArray['key2']);
    }

    public function testNestedArrayOfDtoProperties()
    {
        $data = [
            'dataObjects' => [
                'items' => [
                    ['entityId' => 1, 'name' => 'First'],
                    ['entityId' => 2, 'name' => 'Second']
                ]
            ]
        ];
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
        $this->assertTrue($dataObjects instanceof DataArrayData);
        /** @var array $items */
        $items = $dataObjects->getItems();
        $this->assertEquals(2, count($items));
        /** @var SimpleData $first */
        $first = $items[0];
        /** @var SimpleData $second */
        $second = $items[1];
        $this->assertTrue($first instanceof SimpleData);
        $this->assertEquals(1, $first->getEntityId());
        $this->assertEquals('First', $first->getName());
        $this->assertTrue($second instanceof SimpleData);
        $this->assertEquals(2, $second->getEntityId());
        $this->assertEquals('Second', $second->getName());
    }
}
