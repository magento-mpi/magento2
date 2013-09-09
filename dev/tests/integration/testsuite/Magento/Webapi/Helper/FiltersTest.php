<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Helper_FiltersTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Webapi_Helper_Filters
     */
    private static $_helper;

    public static function setUpBeforeClass()
    {
        self::$_helper = Mage::helper('Magento_Webapi_Helper_Filters');
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
     * @expectedException Magento_Core_Exception
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
            array('{"name":{"$eq":"phone"}}'),
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
            array('{"name":{"$not":{"$eq":"phone"}}}'),
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
            array('{"$or":[{"name":{"$eq":"tablet"}},{"name":{"$eq":"phone"}}]}'),
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
            array('{name:{"$eq":mobile}}'),
            array('{"qty":{"$gt"0}}'),
            array('{"name"{"$eq":tablet}}'),
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
            array('{"name":{"not":{"$eq":"phone"}}}'),
            array('{"category":{"$not":{"eq":"books"}}}'),
            array('{"price":{"not":{"eq":29.99}}}'),
            array('{"name":{"eq":"tablet"}}'),
            array('{"name%#":{"$eq":"tablet"}}'),
            array('{"qty":{"gt":0}}'),
            array('{"is_in_stock":{"foo":false}}'),
            array('{"$and":[{"qty":{"$gt":0}}]}'),
            array('{"$or":[{"is_in_stock":{"ne":false}}]}'),
            array('{"foo":[{"price":{"$gt":19.99}},{"price":{"$lt":49.99}}]}'),
            array('{"$and":[{"qty":{"$gt":0}},{"is.in.stock":{"eq":true}}]}'),
            array('{"$or":[{"qty":{"gt":0}},{"is_in_stock":{"$eq":true}}]}'),
            array('{"$and":[{"name":{"ne":"tablet"}},{"category":{"eq":"tablets"}}]}'),
            array('{"$or":[{"qty$#":{"ne":0}},{"is&in&stock":{"gt":0}}]}'),
            array('{"$and":[{"qty":{"$ne":0}},{"is_in_stock":true},{"status":{"$eq":"active"}}]}'),
        );
    }
}
