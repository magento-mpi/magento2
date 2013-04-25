<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Service
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Service_Helper_FiltersTest extends PHPUnit_Framework_TestCase
{
    private static $_helper;

    public static function setUpBeforeClass()
    {
        self::$_helper = Mage::helper('Mage_Core_Service_Helper_Filters');
    }

    /**
     * @param string $data - Filter query for simple operator expressions (e.g. "$eq", "$lt")
     * @dataProvider operatorExpressionProvider
     */
    public function testOperatorExpression($data)
    {
        $this->assertTrue(self::$_helper->validate($data));
    }

    /**
     * @param string $data - Filter query for unary operator expressions (e.g. "$not")
     * @dataProvider unaryOperatorExpressionProvider
     */
    public function testUnaryOperatorExpression($data)
    {
        $this->assertTrue(self::$_helper->validate($data));
    }

    /**
     * @param string $data - Filter query for binary operator expressions (e.g. "$and", "$or")
     * @dataProvider binaryOperatorExpressionProvider
     */
    public function testBinaryOperatorExpression($data)
    {
        $this->assertTrue(self::$_helper->validate($data));
    }

    /**
     * @param string $data - Invalid JSON for filter query
     * @dataProvider invalidJsonProvider
     * @expectedException JsonSchema\Exception\JsonDecodingException
     */
    public function testInvalidJson($data)
    {
        self::$_helper->validate($data);
    }

    /**
     * @param string $data - Valid JSON filter query that does not validate against the schema
     * @dataProvider invalidSchemaJsonProvider
     * @expectedException Mage_Core_Exception
     */
    public function testInvalidSchemaJson($data)
    {
        self::$_helper->validate($data);
    }

    /**
     * @return array
     */
    public function operatorExpressionProvider()
    {
        return array(
            array('{"name":{"$eq":"iphone"}}'),
            array('{"price":{"$ne":25.00}}'),
            array('{"is_in_stock":{"$eq":true}}'),
            array('{"price":{"$lt":99.99}}'),
            array('{"is-in-stock":{"$ne":false}}'),
            array('{"qty":{"$lte":10}}'),
            array('{"is.available":{"$ne":false}}'),
            array('{"qty":{"$gt":0.00}}'),
            array('{"price":{"$gte":29.99}}'),
            array('{"is_in_stock":{"$ne":false}}')
        );
    }

    /**
     * @return array
     */
    public function unaryOperatorExpressionProvider()
    {
        return array(
            array('{"name":{"$not":{"$eq":"iphone"}}}'),
            array('{"qty":{"$not":{"$ne":0}}}'),
            array('{"is.available":{"$not":{"$eq":false}}}'),
            array('{"price":{"$not":{"$gt":49.99}}}'),
            array('{"is_in_stock":{"$not":{"$eq":false}}}'),
            array('{"qty":{"$not":{"$lt":5}}}')
        );
    }

    /**
     * @return array
     */
    public function binaryOperatorExpressionProvider()
    {
        return array(
            array('{"$and":[{"price":{"$gt":29.99}},{"price":{"$lt":69.99}}]}'),
            array('{"$or":[{"name":{"$eq":"ipod"}},{"name":{"$eq":"iphone"}}]}'),
            array('{"$or":[{"qty":{"$ne":0}},{"is_in_stock":{"$eq":true}},{"status":{"$eq":"active"}}]}'),
            array('{"$and":[{"qty":{"$ne":0}},{"is.available":{"$ne":false}},{"status":{"$eq":"active"}}]}'),
            array('{"$or":[{"qty":{"$not":{"$eq":0}}},{"is-in-stock":{"$not":{"$eq":false}}}]}')
        );
    }

    /**
     * @return array
     */
    public function invalidJsonProvider()
    {
        return array(
            array('{name:{"$eq":iphone}}'),
            array('{"qty":{"$gt"0}}'),
            array('{"name"{"$eq":ipad}}'),
            array('{"is_in_stock":{"$eq":true}'),
            array('{qty:{"$gte":}}')
        );
    }

    /**
     * @return array
     */
    public function invalidSchemaJsonProvider()
    {
        return array(
            array('{"name":{"not":{"$eq":"iphone"}}}'),
            array('{"category":{"$not":{"eq":"books"}}}'),
            array('{"price":{"not":{"eq":29.99}}}'),
            array('{"name":{"eq":"ipad"}}'),
            array('{"name%#":{"$eq":"ipad"}}'),
            array('{"qty":{"gt":0}}'),
            array('{"is_in_stock":{"foo":false}}'),
            array('{"$and":[{"qty":{"$gt":0}}]}'),
            array('{"$or":[{"is_in_stock":{"ne":false}}]}'),
            array('{"foo":[{"price":{"$gt":19.99}},{"price":{"$lt":49.99}}]}'),
            array('{"$and":[{"qty":{"$gt":0}},{"is.in.stock":{"eq":true}}]}'),
            array('{"$or":[{"qty":{"gt":0}},{"is_in_stock":{"$eq":true}}]}'),
            array('{"$and":[{"name":{"ne":"ipad"}},{"category":{"eq":"tablets"}}]}'),
            array('{"$or":[{"qty$#":{"ne":0}},{"is&in&stock":{"gt":0}}]}'),
            array('{"$and":[{"qty":{"$ne":0}},{"is_in_stock":true},{"status":{"$eq":"active"}}]}'),
        );
    }
}
