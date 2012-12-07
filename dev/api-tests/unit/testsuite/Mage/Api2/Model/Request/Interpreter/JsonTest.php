<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Webapi
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test request interpreter JSON adapter
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Webapi_Model_Request_Interpreter_JsonTest extends Mage_PHPUnit_TestCase
{
    /**
     * Test interpret content
     *
     * @dataProvider dataProviderSuccess
     * @param string $encoded
     * @param mixed $decoded
     * @return void
     */
    public function testInterpretContent($encoded, $decoded)
    {
        $adapter = new Mage_Webapi_Model_Request_Interpreter_Json();
        $this->assertEquals($decoded, $adapter->interpret($encoded), 'Decoded data is not like expected.');
    }

    /**
     * Test interpret bad content
     *
     * @dataProvider dataProviderFailure
     * @param $data string
     * @return void
     */
    public function testInterpretBadContent($data)
    {
        try {
            $adapter = new Mage_Webapi_Model_Request_Interpreter_Json();
            $adapter->interpret($data);
        } catch (Mage_Webapi_Exception $e) {
            $this->assertEquals(
                'Decoding error.',
                $e->getMessage(),
                'Invalid argument should produce exception "Decoding error."'
            );
            return;
        }

        $this->fail('Wrong data should throw exception');
    }

    /**
     * Test interpret content not a string
     *
     * @return void
     */
    public function testInterpretContentNotString()
    {
        $adapter = new Mage_Webapi_Model_Request_Interpreter_Json();
        try {
            $adapter->interpret(new stdClass());
        } catch (Exception $e) {
            $this->assertEquals(
                'Invalid data type "object". String expected.',
                $e->getMessage(),
                'Invalid argument should produce exception "Invalid data type".(2)'
            );
            return;
        }

        $this->fail('Invalid argument should produce exception "Invalid data type".(2)');
    }

    /**
     * Provides data for testing error processing
     *
     * @return array
     */
    public function dataProviderFailure()
    {
        return array(
            array(''),
            array('"test1","test2",{"0":"some0","test01":"some1","test02":"some2","1":"some3"]'),
            array('"'),
            array('\\'),
            array('{\}'),
        );
    }

    /**
     * Provides data for testing successful flow
     *
     * @return array
     */
    public function dataProviderSuccess()
    {
        return array(
            array(
                '{"key1":"test1","key2":"test2","array":{"test01":"some1","test02":"some2"}}',
                array(
                    'key1' => 'test1',
                    'key2' => 'test2',
                    'array' => array(
                        'test01' => 'some1',
                        'test02' => 'some2',
                    )
                )),
            array('null', null),
            array('true', true),
            array('1', 1),
            array('1.234', 1.234),
        );
    }
}
