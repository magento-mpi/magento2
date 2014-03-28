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
    protected $_storeManager;

    /**
     * @var \Magento\Webapi\Model\PathProcessor
     */
    protected $_pathProcessor;


    protected function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_storeManager = $objectManager->get('Magento\Core\Model\StoreManagerInterface');
        $this->_pathProcessor = $objectManager->get('\Magento\Webapi\Model\PathProcessor');

    }

    /**
     * @magentoDataFixture Magento/Core/_files/store.php
     */
    public function testProcessStoreWithValidStoreCode()
    {
        $storeCode = 'fixturestore';
        $pathInfo = '/' . $storeCode . '/V1/tomerAccounts/createAccount';
        $this->_pathProcessor->processStore($pathInfo);
        $this->assertEquals($storeCode, $this->_storeManager->getCurrentStore());
    }

    /**
     * @expectedException \Magento\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with storeCode = InvalidStorecode
     */
    public function testProcessStoreInWithValidStoreCode()
    {
        $storeCode = 'InvalidStorecode';
        $pathInfo = '/' . $storeCode . '/V1/tomerAccounts/createAccount';
        $this->_pathProcessor->processStore($pathInfo);
    }
}