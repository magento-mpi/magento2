<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search;

use Magento\TestFramework\Helper\ObjectManager;

class AggregationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Search\Aggregation |\PHPUnit_Framework_MockObject_MockObject
     */
    private $aggregation;

    protected function setUp()
    {
        $helper = new ObjectManager($this);

        $buckets = [];
        for ($count = 0; $count < 5; $count++) {
            $bucket = $this->getMockBuilder('Magento\Framework\Search\Bucket')
                ->disableOriginalConstructor()
                ->getMock();

            $bucket->expects($this->any())->method('getName')->will($this->returnValue("$count"));
            $bucket->expects($this->any())->method('getValue')->will($this->returnValue($count));
            $buckets[] = $bucket;
        }

        $this->aggregation = $helper->getObject(
            '\Magento\Framework\Search\Aggregation',
            [
                'buckets' => $buckets,
            ]
        );
    }

    public function testGetIterator()
    {
        $count = 0;
        foreach ($this->aggregation as $bucket) {
             $this->assertEquals($bucket->getName(), "$count");
             $this->assertEquals($bucket->getValue(), $count);
             $count++;
        }
    }

    public function testGetBucketNames()
    {
        $this->assertEquals(
            $this->aggregation->getBucketNames(),
            ['0', '1', '2', '3', '4']
        );
    }

    public function testGetBucket()
    {
        $bucket = $this->aggregation->getBucket('3');
        $this->assertEquals($bucket->getName(), '3');
        $this->assertEquals($bucket->getValue(), 3);
    }
}
