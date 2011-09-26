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

/**
 * @magentoDataFixture GiftCard/_fixtures/code_pool.php
 * @magentoDataFixture GiftCard/_fixtures/giftcard_account.php
 */
class GiftCard_CartTest extends Magento_Test_Webservice
{
    /**
     * Test giftcard Shopping Cart add, list, remove
     *
     * @return void
     */
    public function testLSD()
    {
        //Test giftcard add to quote
        $giftCardAccount = self::getFixture('giftcard_account');
        $quoteId = $this->call('cart.create', array(1));
        $addResult = $this->call('cart_giftcard.add', array(
            'giftcardAccountCode' => $giftCardAccount->getCode(),
            'quoteId' => $quoteId
        ));
        $this->assertTrue($addResult, 'Add giftcard to quote');

        //Test list of giftcards added to quote
        $giftCards = $this->call('cart_giftcard.list', array('quoteId' => $quoteId));
        $this->assertInternalType('array', $giftCards);
        $this->assertGreaterThan(0, count($giftCards));
        $this->assertEquals($giftCardAccount->getCode(), $giftCards[0]['code']);
        $this->assertEquals($giftCardAccount->getBalance(), $giftCards[0]['base_amount']);

        //Test giftcard removing from quote
        $removeResult = $this->call('cart_giftcard.remove', array(
            'giftcardAccountCode' => $giftCardAccount->getCode(),
            'quoteId' => $quoteId
        ));
        $this->assertTrue($removeResult, 'Remove giftcard from quote');

        //Test giftcard removed
        $this->setExpectedException('Exception');
        $this->call('cart_giftcard.remove', array($giftCardAccount->getCode(), $quoteId));
    }

    /**
     * Test add throw exception with incorrect data
     *
     * @expectedException Exception
     * @return void
     */
    public function testIncorrectDataAddException()
    {
        $fixture = simplexml_load_file(dirname(__FILE__) . '/_fixtures/xml/giftcard_cart.xml');
        $invalidData = self::simpleXmlToArray($fixture->invalid_create);
        $this->call('cart_giftcard.add', $invalidData);
    }

    /**
     * Test list throw exception with incorrect data
     *
     * @expectedException Exception
     * @return void
     */
    public function testIncorrectDataListException()
    {
        $fixture = simplexml_load_file(dirname(__FILE__) . '/_fixtures/xml/giftcard_cart.xml');
        $invalidData = self::simpleXmlToArray($fixture->invalid_list);
        $this->call('cart_giftcard.list', $invalidData);
    }

    /**
     * Test remove throw exception with incorrect data
     *
     * @expectedException Exception
     * @return void
     */
    public function testIncorrectDataRemoveException()
    {
        $fixture = simplexml_load_file(dirname(__FILE__) . '/_fixtures/xml/giftcard_cart.xml');
        $invalidData = self::simpleXmlToArray($fixture->invalid_remove);
        $this->call('cart_giftcard.remove', $invalidData);
    }
}
