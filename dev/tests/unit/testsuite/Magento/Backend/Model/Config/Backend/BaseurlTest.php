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
        $eventDispatcher = $this->getMock('Magento\Framework\Event\ManagerInterface', array(), array(), '', false);
        $appState = $this->getMock('Magento\Framework\App\State', array(), array(), '', false);
        $cacheManager = $this->getMock('Magento\Framework\App\CacheInterface');
        $logger = $this->getMock('Magento\Framework\Logger', array(), array(), '', false);
        $actionValidatorMock = $this->getMock(
            'Magento\Framework\Model\ActionValidator\RemoveAction',
            array(),
            array(),
            '',
            false
        );

        $context = new \Magento\Framework\Model\Context(
            $logger,
            $eventDispatcher,
            $cacheManager,
            $appState,
            $actionValidatorMock
        );

        $resource = $this->getMock('Magento\Core\Model\Resource\Config\Data', array(), array(), '', false);
        $resource->expects($this->any())->method('addCommitCallback')->will($this->returnValue($resource));
        $resourceCollection = $this->getMock('Magento\Framework\Data\Collection\Db', array(), array(), '', false);
        $mergeService = $this->getMock('Magento\Framework\View\Asset\MergeService', array(), array(), '', false);
        $coreRegistry = $this->getMock('Magento\Framework\Registry', array(), array(), '', false);
        $coreConfig = $this->getMock('Magento\Framework\App\Config\ScopeConfigInterface');

        $model = $this->getMock(
            'Magento\Backend\Model\Config\Backend\Baseurl',
            array('getOldValue'),
            array($context, $coreRegistry, $coreConfig, $mergeService, $resource, $resourceCollection)
        );
        $mergeService->expects($this->once())->method('cleanMergedJsCss');

        $model->setValue('http://example.com/')->setPath(\Magento\Store\Model\Store::XML_PATH_UNSECURE_BASE_URL);
        $model->afterSave();
    }
}
