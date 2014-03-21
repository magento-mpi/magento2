<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Product\ProductList;

class ToolbarTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Toolbar
     */
    protected $toolbarModel;

    /**
     * @var \Magento\Stdlib\Cookie |\PHPUnit_Framework_MockObject_MockObject
     */
    protected $cookie;

    /**
     * @var \Magento\App\Request\Http |\PHPUnit_Framework_MockObject_MockObject
     */
    protected $request;

    /**
     * Set up
     */
    public function setUp()
    {
        $this->cookie = $this->getMock('Magento\Stdlib\Cookie', array('get'), array(), '', false);
        $this->request = $this->getMock('Magento\App\Request\Http', array('getParam'), array(), '', false);
        $this->toolbarModel = new Toolbar($this->cookie, $this->request);
    }

    /**
     * @dataProvider stringParamProvider
     * @param $param
     */
    public function testGetOrder($param)
    {
        $this->cookie->expects($this->once())
            ->method('get')
            ->with(Toolbar::ORDER_COOKIE_NAME)
            ->will($this->returnValue($param));
        $this->assertEquals($param, $this->toolbarModel->getOrder());
    }

    /**
     * @dataProvider stringParamProvider
     * @param $param
     */
    public function testGetDirection($param)
    {
        $this->cookie->expects($this->once())
            ->method('get')
            ->with(Toolbar::DIRECTION_COOKIE_NAME)
            ->will($this->returnValue($param));
        $this->assertEquals($param, $this->toolbarModel->getDirection());
    }

    /**
     * @dataProvider stringParamProvider
     * @param $param
     */
    public function testGetMode($param)
    {
        $this->cookie->expects($this->once())
            ->method('get')
            ->with(Toolbar::MODE_COOKIE_NAME)
            ->will($this->returnValue($param));
        $this->assertEquals($param, $this->toolbarModel->getMode());
    }

    /**
     * @dataProvider stringParamProvider
     * @param $param
     */
    public function testGetLimit($param)
    {
        $this->cookie->expects($this->once())
            ->method('get')
            ->with(Toolbar::LIMIT_COOKIE_NAME)
            ->will($this->returnValue($param));
        $this->assertEquals($param, $this->toolbarModel->getLimit());
    }

    /**
     * @dataProvider intParamProvider
     * @param $param
     */
    public function testGetCurrentPage($param)
    {
        $this->request->expects($this->once())
            ->method('getParam')
            ->with(Toolbar::PAGE_PARM_NAME)
            ->will($this->returnValue($param));
        $this->assertEquals($param, $this->toolbarModel->getCurrentPage());
    }

    public function testGetCurrentPageNoParam()
    {
        $this->request->expects($this->once())
            ->method('getParam')
            ->with(Toolbar::PAGE_PARM_NAME)
            ->will($this->returnValue(false));
        $this->assertEquals(1, $this->toolbarModel->getCurrentPage());
    }

    public function stringParamProvider()
    {
        return array(
            array('stringParam')
        );
    }

    public function intParamProvider()
    {
        return array(
            array('2'),
            array(3)
        );
    }
}

