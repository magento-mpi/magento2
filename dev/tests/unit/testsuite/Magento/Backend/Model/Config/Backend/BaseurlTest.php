<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Config\Backend;

class BaseurlTest extends \PHPUnit_Framework_TestCase
{
    public function testSaveMergedJsCssMustBeCleaned()
    {
        $eventDispatcher = $this->getMock('Magento\Event\ManagerInterface', array(), array(), '', false);
        $appState = $this->getMock('Magento\App\State', array(), array(), '', false);
        $storeManager = $this->getMock('Magento\Store\Model\StoreManager', array(), array(), '', false);
        $cacheManager = $this->getMock('Magento\App\CacheInterface');
        $logger = $this->getMock('Magento\Logger', array(), array(), '', false);
        $context = new \Magento\Model\Context(
            $logger,
            $eventDispatcher,
            $cacheManager,
            $appState,
            $storeManager
        );

        $resource = $this->getMock('Magento\Core\Model\Resource\Config\Data', array(), array(), '', false);
        $resource->expects($this->any())
            ->method('addCommitCallback')
            ->will($this->returnValue($resource));
        $resourceCollection = $this->getMock('Magento\Data\Collection\Db', array(), array(), '', false);
        $mergeService = $this->getMock('Magento\View\Asset\MergeService', array(), array(), '', false);
        $coreRegistry = $this->getMock('Magento\Registry', array(), array(), '', false);
        $coreConfig = $this->getMock('Magento\App\ConfigInterface', array(), array(), '', false);
        $storeManager = $this->getMock('Magento\Store\Model\StoreManager', array(), array(), '', false);

        $model = $this->getMock(
            'Magento\Backend\Model\Config\Backend\Baseurl',
            array('getOldValue'),
            array(
                $context,
                $coreRegistry,
                $storeManager,
                $coreConfig,
                $mergeService,
                $resource,
                $resourceCollection
            )
        );
        $mergeService->expects($this->once())
            ->method('cleanMergedJsCss');

        $model->setValue('http://example.com/')
            ->setPath(\Magento\Store\Model\Store::XML_PATH_UNSECURE_BASE_URL);
        $model->save();
    }
}
