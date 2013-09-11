<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Backend_Model_Config_Backend_BaseurlTest extends PHPUnit_Framework_TestCase
{
    public function testSaveMergedJsCssMustBeCleaned()
    {
        $eventDispatcher = $this->getMock('Magento_Core_Model_Event_Manager', array(), array(), '', false);
        $cacheManager = $this->getMock('Magento_Core_Model_CacheInterface');
        $context = new Magento_Core_Model_Context($eventDispatcher, $cacheManager);

        $resource = $this->getMock('Magento_Core_Model_Resource_Config_Data', array(), array(), '', false);
        $resource->expects($this->any())
            ->method('addCommitCallback')
            ->will($this->returnValue($resource));
        $resourceCollection = $this->getMock('Magento_Data_Collection_Db', array(), array(), '', false);
        $mergeService = $this->getMock('Magento_Core_Model_Page_Asset_MergeService', array(), array(), '', false);
        $coreConfig = $this->getMock('Magento_Core_Model_Config', array(), array(), '', false);
        $storeManager = $this->getMock('Magento_Core_Model_StoreManager', array(), array(), '', false);

        $model = $this->getMock(
            'Magento_Backend_Model_Config_Backend_Baseurl',
            array('getOldValue'),
            array($context, $storeManager, $coreConfig, $mergeService, $resource, $resourceCollection)
        );
        $mergeService->expects($this->once())
            ->method('cleanMergedJsCss');

        $model->setValue('http://example.com/')
            ->setPath(Magento_Core_Model_Store::XML_PATH_UNSECURE_BASE_URL);
        $model->save();
    }
}
