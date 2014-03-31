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
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Webapi\Model\PathProcessor
     */
    protected $pathProcessor;


    protected function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->storeManager = $objectManager->get('Magento\Core\Model\StoreManagerInterface');
        $this->pathProcessor = $objectManager->get('\Magento\Webapi\Model\PathProcessor');

    }

    /**
     * @magentoDataFixture Magento/Core/_files/store.php
     */
    public function testProcessStoreWithValidStoreCode()
    {
        $storeCode = 'fixturestore';
        $path = '/' . $storeCode . '/V1/customerAccounts/createAccount';
        $resultPath = $this->pathProcessor->processStore($path);
        $this->assertEquals(str_replace('/' . $storeCode, "", $path), $resultPath);
        $this->assertEquals($storeCode, $this->storeManager->getCurrentStore());
    }

    /**
     * @expectedException \Magento\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with storeCode = InvalidStorecode
     */
    public function testProcessStoreInWithValidStoreCode()
    {
        $storeCode = 'InvalidStorecode';
        $path = '/' . $storeCode . '/V1/customerAccounts/createAccount';
        $this->pathProcessor->processStore($path);
    }
}