<?php
/**
 * Gift card account API model tests.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 * @magentoDbIsolation enabled
 */
class Magento_GiftCardAccount_Model_ApiTest extends PHPUnit_Framework_TestCase
{
    public static $code;

    /**
     * Test create, list, info, update, remove
     *
     * @magentoDataFixture Magento/GiftCardAccount/_files/code_pool.php
     */
    public function testCRUD()
    {
        /** @var Magento_GiftCardAccount_Model_Giftcardaccount $testModel */
        $testModel = Mage::getModel('Magento_GiftCardAccount_Model_Giftcardaccount');
        $accountFixture = simplexml_load_file(
            dirname(__FILE__) . '/../_files/fixture/giftcard_account.xml'
        );

        //Test create
        $createData = Magento_TestFramework_Helper_Api::simpleXmlToArray($accountFixture->create);
        $accountId = Magento_TestFramework_Helper_Api::call($this, 'giftcardAccountCreate', array((object)$createData));
        $this->assertGreaterThan(0, $accountId);

        $testModel->load($accountId);
        // Convert dates to Y-m-d format
        $testModel->setDateCreated(date('Y-m-d', strtotime($testModel->getDateCreated())));
        $testModel->setDateExpires(date('Y-m-d', strtotime($testModel->getDateExpires())));
        $this->_testDataCorrect($createData, $testModel);

        //Test list
        $list = Magento_TestFramework_Helper_Api::call($this, 'giftcardAccountList', array('filters' => array()));
        $this->assertInternalType('array', $list);
        $this->assertGreaterThan(0, count($list));

        //Test info
        $info = Magento_TestFramework_Helper_Api::call($this, 'giftcardAccountInfo',
            array('giftcardAccountId' => $accountId));

        unset($createData['status']);
        unset($createData['website_id']);
        $info['date_expires'] = date('Y-m-d', strtotime($info['expire_date']));
        $this->_testDataCorrect($createData, new \Magento\Object($info));

        //Test update
        $updateData = Magento_TestFramework_Helper_Api::simpleXmlToArray($accountFixture->update);
        $updateResult = Magento_TestFramework_Helper_Api::call($this,
            'giftcardAccountUpdate',
            array('giftcardAccountId' => $accountId, 'giftcardData' => $updateData)
        );
        $this->assertTrue($updateResult);

        $testModel->load($accountId);
        $this->_testDataCorrect($updateData, $testModel);

        //Test remove
        $removeResult = Magento_TestFramework_Helper_Api::call(
            $this,
            'giftcardAccountRemove',
            array('giftcardAccountId' => $accountId)
        );
        $this->assertTrue($removeResult);

        /** @var $pool Magento_GiftCardAccount_Model_Pool */
        $pool = Mage::getModel('Magento_GiftCardAccount_Model_Pool');
        $pool->setCode(self::$code);
        $pool->delete();

        //Test item was really removed and fault was Exception thrown
        Magento_TestFramework_Helper_Api::callWithException($this, 'giftcardAccountRemove',
            array('giftcardAccountId' => $accountId)
        );
    }

    /**
     * Test Exception on invalid data
     */
    public function testCreateExceptionInvalidData()
    {
        $fixture = simplexml_load_file(dirname(__FILE__) . '/../_files/fixture/giftcard_account.xml');
        $invalidCreateData = Magento_TestFramework_Helper_Api::simpleXmlToArray($fixture->invalidCreate);
        Magento_TestFramework_Helper_Api::callWithException($this, 'giftcardAccountCreate', array($invalidCreateData));
    }

    /**
     * Test giftcard account not found exception
     */
    public function testExceptionNotFound()
    {
        $fixture = simplexml_load_file(dirname(__FILE__) . '/../_files/fixture/giftcard_account.xml');
        $invalidData = Magento_TestFramework_Helper_Api::simpleXmlToArray($fixture->invalidInfo);
        Magento_TestFramework_Helper_Api::callWithException($this, 'giftcardAccountInfo',
            array($invalidData['giftcard_id']));
    }

    /**
     * Test that data in db and webservice are equals
     *
     * @param array $data
     * @param \Magento\Object $testModel
     */
    protected function _testDataCorrect($data, $testModel)
    {
        foreach ($data as $testKey => $value) {
            $this->assertEquals($value, $testModel->getData($testKey));
        }
    }
}
