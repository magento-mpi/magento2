<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Api_GiftCard_AccountTest extends Magento_Test_Webservice
{
    /**
     * Test create, list, info, update, remove
     *
     * @magentoDataFixture Api/GiftCard/_fixtures/code_pool.php
     *
     * @return void
     */
    public function testCRUD()
    {
        $testModel = new Enterprise_GiftCardAccount_Model_Giftcardaccount();
        $giftcardAccountFixture = simplexml_load_file(dirname(__FILE__) . '/_fixtures/xml/giftcard_account.xml');

        //Test create
        $createData = self::simpleXmlToArray($giftcardAccountFixture->create);
        $id = $this->call('giftcard_account.create', array('giftcardAccountData' => $createData));
        $this->assertGreaterThan(0, $id);

        $testModel->load($id);
        $this->_testDataCorrect($createData, $testModel);

        //Test list
        $list = $this->call('giftcard_account.list', array('filters' => array()));
        $this->assertInternalType('array', $list);
        $this->assertGreaterThan(0, count($list));

        //Test info
        $info = $this->call('giftcard_account.info', array('giftcardAccountId' => $id));

        unset($createData['status']);
        unset($createData['website_id']);
        $info['date_expires'] = $info['expire_date'];
        $this->_testDataCorrect($createData, new Varien_Object($info));

        //Test update
        $updateData = self::simpleXmlToArray($giftcardAccountFixture->update);
        $updateResult = $this->call(
            'giftcard_account.update', array('giftcardAccountId' => $id, 'giftcardData' => $updateData)
        );
        $this->assertTrue($updateResult);

        $testModel->load($id);
        $this->_testDataCorrect($updateData, $testModel);

        //Test remove
        $removeResult = $this->call('giftcard_account.remove', array('giftcardAccountId' => $id));
        $this->assertTrue($removeResult);

        /** @var $pool Enterprise_GiftCardAccount_Model_Pool */
        $pool = Mage::getModel('enterprise_giftcardaccount/pool');
        $pool->setCode(self::getFixture('giftcardaccount_pool_code'));
        $pool->delete();

        //Test item was really removed and fault was Exception thrown
        $this->setExpectedException(self::DEFAULT_EXCEPTION);
        $this->call('giftcard_account.remove', array('giftcardAccountId' => $id));
    }

    /**
     * Test Exception on invalid data
     *
     * @expectedException DEFAULT_EXCEPTION
     * @return void
     */
    public function _testCreateExceptionInvalidData()
    {
        $fixture = simplexml_load_file(dirname(__FILE__) . '/_fixtures/xml/giftcard_account.xml');

        $invalidCreateData = self::simpleXmlToArray($fixture->invalid_create);
        $this->call('giftcard_account.create', array($invalidCreateData));
    }

    /**
     * Test giftcard account not found exception
     *
     * @expectedException DEFAULT_EXCEPTION
     * @return void
     */
    public function _testExceptionNotFound()
    {
        $fixture = simplexml_load_file(dirname(__FILE__) . '/_fixtures/xml/giftcard_account.xml');

        $invalidData = self::simpleXmlToArray($fixture->invalid_info);
        $this->call('giftcard_account.info', array($invalidData['giftcard_id']));
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
