<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_GiftCardAccount_Model_ApiTest extends PHPUnit_Framework_TestCase
{
    public static $code;

    /**
     * Test create, list, info, update, remove
     *
     * @magentoDataFixture Enterprise/GiftCardAccount/_files/code_pool.php
     *
     * @return void
     */
    public function testCRUD()
    {
        $testModel = Mage::getModel('Enterprise_GiftCardAccount_Model_Giftcardaccount');
        $giftcardAccountFixture = simplexml_load_file(
            dirname(__FILE__) . '/../_files/fixture/giftcard_account.xml'
        );

        //Test create
        $createData = Magento_Test_Helper_Api::simpleXmlToArray($giftcardAccountFixture->create);
        $id = Magento_Test_Helper_Api::call($this, 'giftcardAccountCreate', array((object)$createData));
        $this->assertGreaterThan(0, $id);

        $testModel->load($id);
        $this->_testDataCorrect($createData, $testModel);

        //Test list
        $list = Magento_Test_Helper_Api::call($this, 'giftcardAccountList', array('filters' => array()));
        $this->assertInternalType('array', $list);
        $this->assertGreaterThan(0, count($list));

        //Test info
        $info = Magento_Test_Helper_Api::call($this, 'giftcardAccountInfo', array('giftcardAccountId' => $id));

        unset($createData['status']);
        unset($createData['website_id']);
        $info['date_expires'] = $info['expire_date'];
        $this->_testDataCorrect($createData, new Varien_Object($info));

        //Test update
        $updateData = Magento_Test_Helper_Api::simpleXmlToArray($giftcardAccountFixture->update);
        $updateResult = Magento_Test_Helper_Api::call($this,
            'giftcardAccountUpdate',
            array('giftcardAccountId' => $id, 'giftcardData' => $updateData)
        );
        $this->assertTrue($updateResult);

        $testModel->load($id);
        $this->_testDataCorrect($updateData, $testModel);

        //Test remove
        $removeResult = Magento_Test_Helper_Api::call(
            $this,
            'giftcardAccountRemove',
            array('giftcardAccountId' => $id)
        );
        $this->assertTrue($removeResult);

        /** @var $pool Enterprise_GiftCardAccount_Model_Pool */
        $pool = Mage::getModel('Enterprise_GiftCardAccount_Model_Pool');
        $pool->setCode(self::$code);
        $pool->delete();

        //Test item was really removed and fault was Exception thrown
        $this->setExpectedException('SoapFault');
        Magento_Test_Helper_Api::call($this, 'giftcardAccountRemove', array('giftcardAccountId' => $id));
    }

    /**
     * Test Exception on invalid data
     *
     * @expectedException SoapFault
     * @return void
     */
    public function testCreateExceptionInvalidData()
    {
        $fixture = simplexml_load_file(dirname(__FILE__) . '/../_files/fixture/giftcard_account.xml');

        $invalidCreateData = Magento_Test_Helper_Api::simpleXmlToArray($fixture->invalid_create);
        Magento_Test_Helper_Api::call($this, 'giftcardAccountCreate', array($invalidCreateData));
    }

    /**
     * Test giftcard account not found exception
     *
     * @expectedException SoapFault
     * @return void
     */
    public function testExceptionNotFound()
    {
        $fixture = simplexml_load_file(dirname(__FILE__) . '/../_files/fixture/giftcard_account.xml');

        $invalidData = Magento_Test_Helper_Api::simpleXmlToArray($fixture->invalid_info);
        Magento_Test_Helper_Api::call($this, 'giftcardAccountInfo', array($invalidData->giftcard_id));
    }

    /**
     * Test that data in db and webservice are equals
     *
     * @param array $data
     * @param Varien_Object $testModel
     * @return void
     */
    protected function _testDataCorrect($data, $testModel)
    {
        foreach ($data as $testKey => $value) {
            $this->assertEquals($value, $testModel->getData($testKey));
        }
    }
}
