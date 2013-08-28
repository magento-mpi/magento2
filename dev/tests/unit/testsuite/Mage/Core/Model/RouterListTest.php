<?php
/**
 * RouterList model test class
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_RouterListTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_RouterList
     */
    protected $_model;

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectMangerMock;

    /**
     * @var array
     */
    protected $_routerList;

    protected function setUp()
    {
        $this->_routerList = array(
            'adminRouter' => array(
                'class'     => 'AdminClass',
                'disable'   => true,
                'sortOrder' => 10
            ),
            'frontendRouter' => array(
                'class'     => 'FrontClass',
                'disable'   => false,
                'sortOrder' => 10
            ),
            'defaultRouter' => array(
                'class'     => 'DefaultClass',
                'disable'   => false,
                'sortOrder' => 5
            ),
        );

        $this->_objectMangerMock = $this->getMock('Magento_ObjectManager');
        $this->_model = new Mage_Core_Model_RouterList($this->_objectMangerMock, $this->_routerList);
    }

    public function testGetRoutes()
    {
        $expectedResult = array(
            'defaultRouter'  => new DefaultClass(),
            'frontendRouter' => new FrontClass(),
        );

        $this->_objectMangerMock->expects($this->at(0))->method('create')->will($this->returnValue(new DefaultClass()));
        $this->_objectMangerMock->expects($this->at(1))->method('create')->will($this->returnValue(new FrontClass()));

        $this->assertEquals($this->_model->getRouters(), $expectedResult);
    }
}

class FrontClass
{

}
class DefaultClass
{

}
