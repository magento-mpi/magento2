<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_DesignEditor_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_DesignEditor_Helper_Data */
    protected $_helper;

    public function setUp()
    {
        $this->_helper = new Mage_DesignEditor_Helper_Data();
    }

    protected function tearDown()
    {
        unset($this->_helper);
    }

    /**
     * @dataProvider vdeRequestDataProvider
     * @param string $url
     * @param bool $expectedResult
     */
    public function testIsVdeRequest($url, $expectedResult)
    {
        /** @var $request Mage_Core_Controller_Request_Http */
        $request = $this->getMock('Mage_Core_Controller_Request_Http',
            array('getOriginalPathInfo'), array(), '', false
        );
        $request->expects($this->once())
            ->method('getOriginalPathInfo')
            ->will($this->returnValue($url));

        $this->assertEquals($expectedResult, $this->_helper->isVdeRequest($request));
    }

    /**
     * Data Provider for IsVdeRequest method
     */
    public function vdeRequestDataProvider()
    {
        return array(
            'plane vde prefix' => array(
                'url' => Mage_DesignEditor_Helper_Data::FRONT_NAME,
                'expected result' => true
            ),
            'vde prefix with slashes' => array(
                'url' => '/'. Mage_DesignEditor_Helper_Data::FRONT_NAME . '/',
                'expected result' => true
            ),
            'vde prefix with action' => array(
                'url' => Mage_DesignEditor_Helper_Data::FRONT_NAME . '/run',
                'expected result' => true
            ),
            'not valid vde prefix' => array(
                'url' => Mage_Core_Model_App_Area::AREA_FRONTEND . '/' . Mage_DesignEditor_Helper_Data::FRONT_NAME,
                'expected result' => false
            )
        );
    }
}
