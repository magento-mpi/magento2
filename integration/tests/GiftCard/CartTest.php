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

class GiftCard_CartTest extends Magento_Test_Webservice
{
    /**
     * Giftcard account instance
     *
     * @var Enterprise_GiftCardAccount_Model_Giftcardaccount
     */
    protected $_giftcardAccount;

    /**
     * @return void
     */
    protected function setUp()
    {
        require dirname(__FILE__) . '/_fixtures/code_pool.php';
        $accountFixture = simplexml_load_file(dirname(__FILE__) . '/_fixtures/xml/giftcard_account.xml');
        $accountCreateData = self::simpleXmlToArray($accountFixture->create);

        $giftcardAccount = new Enterprise_GiftCardAccount_Model_Giftcardaccount();
        $giftcardAccount->setData($accountCreateData);
        $giftcardAccount->save();

        $this->_giftcardAccount = $giftcardAccount;
    }

    protected function tearDown()
    {
        if ($this->_giftcardAccount) {
            $this->_giftcardAccount->delete();
            unset($this->_giftcardAccount);
        }
    }

    /**
     * Test giftcard Shopping Cart add, list, remove
     *
     * @return void
     */
    public function testLSD()
    {
        //Test giftcard att to quote
        $quoteId = $this->call('cart.create', array(1));
        $addResult = $this->call('cart_giftcard.add', array($this->_giftcardAccount->getCode(), $quoteId));
        $this->assertTrue($addResult, 'Add giftcard to quote');

        //Test list of giftcards added to quote
        $giftcards = $this->call('cart_giftcard.list', array($quoteId));
        $this->assertGreaterThan(0, count($giftcards));
        $this->assertEquals($this->_giftcardAccount->getCode(), $giftcards[0]['code']);
        $this->assertEquals($this->_giftcardAccount->getBalance(), $giftcards[0]['base_amount']);

        //Test giftcard removing from quote
        $removeResult = $this->call('cart_giftcard.remove', array($this->_giftcardAccount->getCode(), $quoteId));
        $this->assertTrue($removeResult, 'Remove giftcard from quote');
    }

    /**
     * Test add throw exception with incorrect data
     *
     * @return void
     */
    public function testIncorrectDataAddException()
    {
        $this->setExpectedException('Exception');

        $fixture = simplexml_load_file(dirname(__FILE__) . '/_fixtures/xml/giftcard_cart.xml');
        $invalidData = self::simpleXmlToArray($fixture->invalid_create);
        $this->call('cart_giftcard.add', $invalidData);
    }

    /**
     * Test list throw exception with incorrect data
     *
     * @return void
     */
    public function testIncorrectDataListException()
    {
        $this->setExpectedException('Exception');

        $fixture = simplexml_load_file(dirname(__FILE__) . '/_fixtures/xml/giftcard_cart.xml');
        $invalidData = self::simpleXmlToArray($fixture->invalid_list);
        $this->call('cart_giftcard.list', $invalidData);
    }

    /**
     * Test remove throw exception with incorrect data
     *
     * @return void
     */
    public function testIncorrectDataRemoveException()
    {
        $this->setExpectedException('Exception');

        $fixture = simplexml_load_file(dirname(__FILE__) . '/_fixtures/xml/giftcard_cart.xml');
        $invalidData = self::simpleXmlToArray($fixture->invalid_remove);
        $this->call('cart_giftcard.remove', $invalidData);
    }
}
