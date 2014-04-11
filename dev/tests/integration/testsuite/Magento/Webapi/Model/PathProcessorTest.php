<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webapi\Model;

class PathProcessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Webapi\Model\PathProcessor
     */
    protected $pathProcessor;


    protected function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->storeManager = $objectManager->get('Magento\Store\Model\StoreManagerInterface');
        $this->pathProcessor = $objectManager->get('\Magento\Webapi\Model\PathProcessor');

    }

    /**
     * @magentoDataFixture Magento/Core/_files/store.php
     */
    public function testProcessWithValidStoreCode()
    {
        $storeCode = 'fixturestore';
        $basePath = "rest/{$storeCode}";
        $path = $basePath . '/V1/customerAccounts/createAccount';
        $resultPath = $this->pathProcessor->process($path);
        $this->assertEquals(str_replace($basePath, "", $path), $resultPath);
        $this->assertEquals($storeCode, $this->storeManager->getCurrentStore());
    }

    /**
     * @expectedException \Magento\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with storeCode = InvalidStorecode
     */
    public function testProcessWithInValidStoreCode()
    {
        $storeCode = 'InvalidStorecode';
        $path = '/rest/' . $storeCode . '/V1/customerAccounts/createAccount';
        $this->pathProcessor->process($path);
    }
}
