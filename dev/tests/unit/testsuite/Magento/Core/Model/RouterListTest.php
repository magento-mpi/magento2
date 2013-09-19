<?php
/**
 * RouterList model test class
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model;

class RouterListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\RouterList
     */
    protected $_model;

    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManagerMock;

    /**
     * @var array
     */
    protected $_routerList;

    protected function setUp()
    {
        $this->_routerList = array(
            'adminRouter' => array(
                'instance'     => 'AdminClass',
                'disable'   => true,
                'sortOrder' => 10
            ),
            'frontendRouter' => array(
                'instance'     => '\FrontClass',
                'disable'   => false,
                'sortOrder' => 10
            ),
            'defaultRouter' => array(
                'instance'     => '\DefaultClass',
                'disable'   => false,
                'sortOrder' => 5
            ),
        );

        $this->_objectManagerMock = $this->getMock('Magento\ObjectManager');
        $this->_model = new \Magento\Core\Model\RouterList($this->_objectManagerMock, $this->_routerList);
    }

    public function testGetRoutes()
    {
        $expectedResult = array(
            'defaultRouter'  => new \DefaultClass(),
            'frontendRouter' => new \FrontClass(),
        );

        $this->_objectManagerMock
            ->expects($this->at(0))
            ->method('create')
            ->will($this->returnValue(new \DefaultClass()));
        $this->_objectManagerMock
            ->expects($this->at(1))
            ->method('create')
            ->will($this->returnValue(new \FrontClass()));

        $this->assertEquals($this->_model->getRouters(), $expectedResult);
    }
}

class FrontClass
{

}
class DefaultClass
{

}
