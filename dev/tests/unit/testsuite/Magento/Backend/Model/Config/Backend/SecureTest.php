<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Backend_Model_Config_Backend_SecureTest extends PHPUnit_Framework_TestCase
{
    public function testSaveMergedJsCssMustBeCleaned()
    {
        $eventDispatcher = $this->getMock('Magento\Core\Model\Event\Manager', array(), array(), '', false);
        $cacheManager = $this->getMock('Magento\Core\Model\CacheInterface');
        $context = new \Magento\Core\Model\Context($eventDispatcher, $cacheManager);

        $resource = $this->getMock('Magento\Core\Model\Resource\Config\Data', array(), array(), '', false);
        $resource->expects($this->any())
            ->method('addCommitCallback')
            ->will($this->returnValue($resource));
        $resourceCollection = $this->getMock('Magento\Data\Collection\Db', array(), array(), '', false);
        $mergeService = $this->getMock('Magento\Core\Model\Page\Asset\MergeService', array(), array(), '', false);
        $coreRegistry = $this->getMock('Magento\Core\Model\Registry', array(), array(), '', false);
        $coreConfig = $this->getMock('Magento\Core\Model\Config', array(), array(), '', false);
        $storeManager = $this->getMock('Magento\Core\Model\StoreManager', array(), array(), '', false);

        $model = $this->getMock(
            'Magento\Backend\Model\Config\Backend\Secure',
            array('getOldValue'),
            array($context, $coreRegistry, $storeManager, $coreConfig, $mergeService, $resource, $resourceCollection)
        );
        $mergeService->expects($this->once())
            ->method('cleanMergedJsCss');

        $model->setValue('new_value');
        $model->save();
    }
}
