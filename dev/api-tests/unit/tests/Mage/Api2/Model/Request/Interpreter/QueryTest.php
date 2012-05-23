<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Api2
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test request interpreter query adapter
 *
 * @category    Mage
 * @package     Mage_Api2
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Request_Interpreter_QueryTest extends Mage_PHPUnit_TestCase
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
        $adapter = new Mage_Api2_Model_Request_Interpreter_Query();
        $this->assertEquals($decoded, $adapter->interpret($encoded), 'Decoded data is not what is expected.');
    }

    /**
     * Test interpret content not a string
     *
     * @return void
     */
    public function testInterpretContentNotString()
    {
        $adapter = new Mage_Api2_Model_Request_Interpreter_Query();
        try {
            $adapter->interpret(new stdClass());
        } catch (Exception $e) {
            $this->assertEquals(
                'Invalid data type "object". String expected.',
                $e->getMessage(),
                'Invalid argument should produce exception "Invalid data type".'
            );
            return;
        }

        $this->fail('Invalid argument should produce exception "Invalid data type".(2)');
    }

    /**
     * Test interpret content not valid
     *
     * @dataProvider dataProviderFailed
     * @param string $encoded
     * @return void
     */
    public function testInterpretContentNotValid($encoded)
    {
        $adapter = new Mage_Api2_Model_Request_Interpreter_Query();

        $this->setExpectedException('Mage_Api2_Exception', 'Invalid data type. Check Content-Type.');

        $adapter->interpret($encoded);
    }

    /**
     * Provides data for testing successful flow
     *
     * @return array
     */
    public function dataProviderSuccess()
    {
        return array(
            array('foo', array('foo' => '')),
            array('1', array('1'=>'')),
            array('1.234', array('1_234'=>'')),
            array('foo=bar', array('foo'=>'bar')),
            array('foo=bar=', array('foo'=>'bar=')),
            array(
                'key1=test1&key2=test2&array[test01]=some1&array[test02]=some2',
                array(
                    'key1' => 'test1',
                    'key2' => 'test2',
                    'array' => array(
                        'test01' => 'some1',
                        'test02' => 'some2',
                    )
                )
            ),
        );
    }

    /**
     * Provides data for testing failed flow
     *
     * @return array
     */
    public function dataProviderFailed()
    {
        return array(
            array('foo bar'),
            array('foo=>bar'),
            array('foo
            bar')
        );
    }
}
