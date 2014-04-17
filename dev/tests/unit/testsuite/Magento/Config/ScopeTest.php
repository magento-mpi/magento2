<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Config;

class ScopeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Config\Scope
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\App\AreaList
     */
    protected $areaListMock;

    protected function setUp()
    {
        $this->areaListMock = $this->getMock('Magento\Framework\App\AreaList', array('getCodes'), array(), '', false);
        $this->model = new Scope($this->areaListMock);
    }

    public function testScopeSetGet()
    {
        $scopeName = 'test_scope';
        $this->model->setCurrentScope($scopeName);
        $this->assertEquals($scopeName, $this->model->getCurrentScope());
    }

    public function testGetAllScopes()
    {
        $expectedBalances = array('primary', 'test_scope');
        $this->areaListMock->expects($this->once())
            ->method('getCodes')
            ->will($this->returnValue(array('test_scope')));
        $this->assertEquals($expectedBalances, $this->model->getAllScopes());
    }
}
