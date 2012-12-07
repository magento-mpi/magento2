<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @copyright   {copyright}
 * @license     {license_link}
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
