<?php
/**
 * RouterList model test class
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App;

class RouterListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\App\RouterList
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
            'adminRouter' => array('class' => 'AdminClass', 'disable' => true, 'sortOrder' => 10),
            'frontendRouter' => array('class' => 'FrontClass', 'disable' => false, 'sortOrder' => 10),
            'default' => array('class' => 'DefaultClass', 'disable' => false, 'sortOrder' => 5)
        );

        $this->_objectManagerMock = $this->getMock('Magento\ObjectManager');
        $this->_model = new \Magento\Framework\App\RouterList($this->_objectManagerMock, $this->_routerList);
    }

    public function testGetRoutes()
    {
        $expectedClass = new FrontClass();
        $this->_objectManagerMock->expects($this->at(0))->method('create')->will($this->returnValue($expectedClass));

        $this->assertEquals($expectedClass, $this->_model->current('frontendRouter'));
    }
}
