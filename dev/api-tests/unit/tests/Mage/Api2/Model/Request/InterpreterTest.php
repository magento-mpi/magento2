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
 * Request content interpreter factory
 *
 * @category    Mage
 * @package     Mage_Api2
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Request_InterpreterTest extends Mage_PHPUnit_TestCase
{
    /**
     * API2 data helper mock
     *
     * @var Mage_Api2_Helper_Data
     */
    protected $_helperMock;

    /**
     * API2 interpreters data fixture
     *
     * @var array
     */
    protected $_interpreters;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->_interpreters = (array) simplexml_load_file(dirname(__FILE__) . '/_fixtures/xml/interpreters.xml');
        $this->_helperMock = $this->getHelperMockBuilder('api2')->getMock();
    }

    /**
     * Test request content interpreter factory
     *
     * @return void
     */
    public function testFactoryInputTypes()
    {
        $this->_helperMock->expects($this->any())
            ->method('getRequestInterpreterAdapters')
            ->will($this->returnValue($this->_interpreters));

        $data = array(
            'application/json'      => 'Mage_Api2_Model_Request_Interpreter_Json',
            'text/plain'            => 'Mage_Api2_Model_Request_Interpreter_Query',
            'application/xml'       => 'Mage_Api2_Model_Request_Interpreter_Xml',
            'application/xhtml+xml' => 'Mage_Api2_Model_Request_Interpreter_Xml',
            'text/xml'              => 'Mage_Api2_Model_Request_Interpreter_Xml'
        );
        foreach ($data as $type => $expectedClass) {
            $interpreter = Mage_Api2_Model_Request_Interpreter::factory($type);
            $this->assertInstanceOf($expectedClass, $interpreter);
        }
    }

    /**
     * Test request content interpreter factory with unknown accept type
     *
     * @expectedException Mage_Api2_Exception
     * @return void
     */
    public function testFactoryBadAcceptType()
    {
        $this->_helperMock->expects($this->any())
            ->method('getRequestInterpreterAdapters')
            ->will($this->returnValue($this->_interpreters));

        /**
         * Try get adapter via invalid content type
         * and must be throw exception
         */
        Mage_Api2_Model_Request_Interpreter::factory('unknown/unknown');
    }
}
