<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @category    Mage
 * @package     Mage_PHPUnit
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * One small unit test for Mage_PHPUnit_Db_Helper_Query file.
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PHPUnit_Db_Helper_QueryTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test
     *
     * @dataProvider providerSql
     */
    public function testCompress($sql, $expectedSql)
    {
        require_once dirname(__FILE__).'/Query.php';
        $this->assertEquals($expectedSql, Mage_PHPUnit_Db_Helper_Query::compress($sql));
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function providerSql()
    {
        return array(
            array('SELECT *  FROM `xxx`    where
                    sss = 111', 'SELECT*FROM`xxx`where sss=111'),
            array('SELECT *  FROM```xxx```    where
                    sss=\'111\' ', 'SELECT*FROM```xxx```where sss=\'111\''),
            array('SELECT *  FROM xxx    where
                    sss=\'111\' ', 'SELECT*FROM xxx where sss=\'111\''),
            array('SELECT a.*, b . xxx   FROM xxx    where
                    sss=\'#abs\' or a . `bbb` > \'\\\'ccc\\\\\'  ', 'SELECT a.*,b.xxx FROM xxx where sss=\'#abs\'or a.`bbb`>\'\\\'ccc\\\\\''),
            array('SELECT Андрей () ', 'SELECT Андрей()'),
            array("SELECT \"qvc_customer_reused_card\".* FROM \"qvc_customer_reused_card\" WHERE (qvc_customer_reused_card.customer_id=999) AND (last4_digits = '9876') AND (exp_month = '4') AND (exp_year = '2087') AND (type = 'VI') AND (name = 'Alex Kusakin')",
                  "SELECT\"qvc_customer_reused_card\".*FROM\"qvc_customer_reused_card\"WHERE(qvc_customer_reused_card.customer_id=999)AND(last4_digits='9876')AND(exp_month='4')AND(exp_year='2087')AND(type='VI')AND(name='Alex Kusakin')")
        );
    }
}
