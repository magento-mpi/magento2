<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backup
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backup\Helper;

class DataTest extends \PHPUnit_Framework_TestCase
{
    public function testInvalidateIndexer()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $process = $this->getMockBuilder('Magento\Index\Model\Process')
            ->disableOriginalConstructor()
            ->getMock();
        $process->expects($this->once())
            ->method('changeStatus')
            ->with(\Magento\Index\Model\Process::STATUS_REQUIRE_REINDEX);
        $iterator = $this->returnValue(new \ArrayIterator(array($process)));

        $collection = $this->getMockBuilder('Magento\Index\Model\Resource\Process\Collection')
            ->disableOriginalConstructor()
            ->getMock();
        $collection->expects($this->at(0))->method('getIterator')
            ->will($iterator);

        $processFactory = $this->getMockBuilder('Magento\Index\Model\Resource\Process\CollectionFactory')
            ->setMethods(array('create'))
            ->disableOriginalConstructor()
            ->getMock();
        $processFactory->expects($this->any())
            ->method('create')
            ->will($this->returnValue($collection));

        $object = $helper->getObject('Magento\Backup\Helper\Data', array(
            'processFactory' => $processFactory
        ));
        $object->invalidateIndexer();
    }
}
