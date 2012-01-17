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
 * Test Api2 config model
 */
class Mage_Api2_Model_Request_Interpreter_JsonTest extends Magento_TestCase
{
    protected $_fixture;

    /**
     * Get fixture data
     *
     * @return array
     */
    protected function _getFixture()
    {
        if (null === $this->_fixture) {
            $this->_fixture = require dirname(__FILE__) . '/_fixture/json.php';
        }
        return $this->_fixture;
    }

    /**
     * Test interpret JSON content
     */
    function testInterpretContent()
    {
        $data = $this->_getFixture();
        $adapter = new Mage_Api2_Model_Request_Interpreter_Json();
        $this->assertEquals(
            $data['decoded'],
            $adapter->interpret($data['encoded']),
            'JSON decoded data is not like expected.');
    }

    /**
     * Test interpret bad JSON content
     */
    function testInterpretBadContent()
    {
        $data = $this->_getFixture();
        $adapter = new Mage_Api2_Model_Request_Interpreter_Json();
        $this->assertEmpty(
            $adapter->interpret($data['invalid_encoded']),
            'Result of decode bad JSON string should be empty.');
    }
}
