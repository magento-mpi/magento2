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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Paas
 * @package     integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * CustomerBalance tests
 *
 * @category   Paas
 * @package    integration_tests
 * @author     Magento PaaS Team <paas-team@magento.com>
 */

/**
 * @magentoDataFixture CustomerBalance/_fixtures/Quote.php
 */
class CustomerBalance_QuoteTest extends Magento_Test_Webservice
{

    public static $quoteId = null;

    /**
     * Test successful customer balance set amount to quote
     *
     * @return void
     */
    public function testCustomerBalanceForQuoteSetAmount()
    {
        //$quoteFixture = simplexml_load_file(dirname(__FILE__).'/_fixtures/CustomerBalanceForQuoteSetAmount.xml');
        //$data = self::simpleXmlToArray($quoteFixture);

        $result = $this->call('storecredit_quote.setAmount', array('quote_id'=> self::$quoteId, 'store_id' => 1));

        var_dump($result);
    }

}
