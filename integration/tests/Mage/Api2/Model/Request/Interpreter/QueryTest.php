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
 * @package     Mage_Api2
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test request interpreter query adapter
 *
 * @category   Mage
 * @package    Mage_Api2
 * @subpackage integration_tests
 * @author     Magento Api Team <apia-team@magento.com>
 */
class Mage_Api2_Model_Request_Interpreter_QueryTest extends Magento_TestCase
{

    /**
     * Test interpret content
     *
     * @dataProvider dataProviderSuccess
     * @param string $encoded
     * @param mixed $decoded
     */
    public function testInterpretContent($encoded, $decoded)
    {
        $adapter = new Mage_Api2_Model_Request_Interpreter_Query();
        $this->assertEquals($decoded, $adapter->interpret($encoded), 'Decoded data is not what is expected.');
    }

    /**
     * Test interpret bad content
     *
     * @dataProvider dataProviderFailure
     * @param $data string
     */
    public function testInterpretBadContent($data)
    {
        //NOTE: Interpreter QUERY adapter always return array
        try {
            $adapter = new Mage_Api2_Model_Request_Interpreter_Query();
            $adapter->interpret($data);
        } catch (Mage_Api2_Exception $e) {
            $this->assertEquals(
                'Decoding error.',
                $e->getMessage(),
                'Invalid argument should produce exception "Decoding error."'
            );
            return;
        }

        $this->fail('Invalid argument should produce exception "Decoding error.(2)"');
    }

    /**
     * Test interpret content not a string
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
     * Provides data for testing error processing
     *
     * @return array
     */
    public function dataProviderFailure()
    {
        return array(
            array('&'),
            array('='),
            array(''),
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
            array('foo', array('foo'=>'')),
            array('foo bar', array('foo_bar'=>'')),
            array('1', array('1'=>'')),
            array('1.234', array('1_234'=>'')),
            array('foo=bar', array('foo'=>'bar')),
            array('foo=>bar', array('foo'=>'>bar')),
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
                )),
        );
    }
}
