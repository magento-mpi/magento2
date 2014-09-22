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
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Webapi\Model\PathProcessor
     */
    protected $pathProcessor;


    protected function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->storeManager = $objectManager->get('Magento\Framework\StoreManagerInterface');
        $this->pathProcessor = $objectManager->get('\Magento\Webapi\Model\PathProcessor');

    }

    /**
     * @magentoDataFixture Magento/Core/_files/store.php
     */
    public function testProcessWithValidStoreCode()
    {
        $storeCode = 'fixturestore';
        $basePath = "rest/{$storeCode}";
        $path = $basePath . '/V1/customerAccounts/createCustomer';
        $resultPath = $this->pathProcessor->process($path);
        $this->assertEquals(str_replace($basePath, "", $path), $resultPath);
        $this->assertEquals($storeCode, $this->storeManager->getStore()->getCode());
    }

    public function testProcessWithoutStoreCode()
    {
        $path = 'rest/V1/customerAccounts/createCustomer';
        $result = $this->pathProcessor->process($path);
        $this->assertEquals('/V1/customerAccounts/createCustomer', $result);
        $this->assertEquals('default', $this->storeManager->getStore()->getCode());
    }
}
