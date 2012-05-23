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
