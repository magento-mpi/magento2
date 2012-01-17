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
 * Test request interpreter JSON adapter
 *
 * @category   Mage
 * @package    Mage_Api2
 * @subpackage integration_tests
 * @author     Magento Api Team <apia-team@magento.com>
 */
class Mage_Api2_Model_Request_Interpreter_JsonTest extends Magento_TestCase
{
    /**
     * Content fixture
     *
     * @var array
     */
    protected $_fixture;

    /**
     * Get fixture data
     *
     * @return array
     */
    protected function _getFixture()
    {
        if (null === $this->_fixture) {
            $this->_fixture = require dirname(__FILE__) . '/_fixture/typesContent.php';
        }
        return $this->_fixture;
    }

    /**
     * Test interpret content
     */
    function testInterpretContent()
    {
        $data = $this->_getFixture();
        $adapter = new Mage_Api2_Model_Request_Interpreter_Json();
        $this->assertEquals(
            $data['decoded'],
            $adapter->interpret($data['json_encoded']),
            'Decoded data is not like expected.');
    }

    /**
     * Test interpret bad content
     */
    function testInterpretBadContent()
    {
        $data = $this->_getFixture();
        $adapter = new Mage_Api2_Model_Request_Interpreter_Json();
        $this->assertEmpty(
            $adapter->interpret($data['json_invalid_encoded']),
            'Result of decode bad string should be empty.');
    }

    /**
     * Test interpret content not a string
     */
    function testInterpretContentNotString()
    {
        $adapter = new Mage_Api2_Model_Request_Interpreter_Json();
        try {
            $adapter->interpret(new stdClass());
        } catch (Exception $e) {
            $this->assertEquals(
                'Content is not a string.',
                $e->getMessage(),
                'Unknown exception on interpret not a string value.');
        }
    }
}
