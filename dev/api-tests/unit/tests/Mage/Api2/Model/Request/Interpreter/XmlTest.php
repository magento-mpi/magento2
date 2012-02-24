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
 * @subpackage  unit_tests
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test request interpreter XML adapter
 *
 * @category    Mage
 * @package     Mage_Api2
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Request_Interpreter_XmlTest extends Mage_PHPUnit_TestCase
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
        $adapter = new Mage_Api2_Model_Request_Interpreter_Xml();
        $this->assertEquals($decoded, $adapter->interpret($encoded), 'Decoded data is not what is expected.');
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
            $adapter = new Mage_Api2_Model_Request_Interpreter_Xml();
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
     *
     * @return void
     */
    public function testInterpretContentNotString()
    {
        $adapter = new Mage_Api2_Model_Request_Interpreter_Xml();
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
            array('<'),
            array('<root'),
            array('<root>'),
            array('<root><node>'),
            array('<root><node></node>'),
            array('<root><node></root>'),
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
            array('<root></root>', array()),
            array('<root />', array()),
            array('<root>1</root>', array()),
            array('<root><node /></root>', array('node'=>'')),
            array('<?xml version="1.0"?><xml><key1>test1</key1><key2>test2</key2><array><test01>some1</test01>
                    <test02>some2</test02></array></xml>',
                array(
                    'key1' => 'test1',
                    'key2' => 'test2',
                    'array' => array(
                        'test01' => 'some1',
                        'test02' => 'some2',
            ))),
            array('<xml><key1>test1</key1><key2>test2</key2><array><test01>some1</test01>
                    <test02>some2</test02></array></xml>',
                array(
                    'key1' => 'test1',
                    'key2' => 'test2',
                    'array' => array(
                        'test01' => 'some1',
                        'test02' => 'some2',
            ))),
            array(
                '<root>
                  <array attr1="1">
                      <attr2>2</attr2>
                  </array>
                  <array attr1="1">
                      <attr2>2</attr2>
                  </array>
                 </root>',
                array(
                    'array' => array(
                        array('attr1' => 1, 'attr2' => 2),
                        array('attr1' => 1, 'attr2' => 2),
                    )
                )
            ),
        );
    }
}
