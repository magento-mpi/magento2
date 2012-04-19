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
class Mage_Api2_Model_RendererTest extends Mage_PHPUnit_TestCase
{
    /**
     * API2 data helper mock
     *
     * @var Mage_Api2_Helper_Data
     */
    protected $_helperMock;

    /**
     * API2 renders data fixture
     *
     * @var array
     */
    protected $_renders;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->_renders = (array) simplexml_load_file(dirname(__FILE__) . '/_fixtures/xml/renders.xml');
        $this->_helperMock = $this->getHelperMockBuilder('api2')->getMock();
    }

    /**
     * Test response content renderer factory
     *
     * @return void
     */
    public function testFactory()
    {
        $this->_helperMock->expects($this->any())
            ->method('getResponseRenderAdapters')
            ->will($this->returnValue($this->_renders));

        $data = array(
            '*/*'               => 'Mage_Api2_Model_Renderer_Json',
            'application/*'     => 'Mage_Api2_Model_Renderer_Json',
            'application/json'  => 'Mage_Api2_Model_Renderer_Json',
            'application/xml'   => 'Mage_Api2_Model_Renderer_Xml',
            'text/plain'        => 'Mage_Api2_Model_Renderer_Query'
        );
        foreach ($data as $type => $expectedClass) {
            $adapter = Mage_Api2_Model_Renderer::factory($type);
            $this->assertInstanceOf($expectedClass, $adapter);
        }
    }

    /**
     * Test response content renderer factory with unknown accept type
     *
     * @expectedException Mage_Api2_Exception
     * @return void
     */
    public function testFactoryBadAcceptType()
    {
        $this->_helperMock->expects($this->any())
            ->method('getResponseRenderAdapters')
            ->will($this->returnValue($this->_renders));

        /**
         * Try get adapter via invalid content type
         * and must be throw exception
         */
        Mage_Api2_Model_Renderer::factory('unknown/unknown');
    }
}
