<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model;

class DesignLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\View\DesignLoader
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_areaListMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    /**
     * @var \Magento\Framework\App\State|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $appState;

    protected function setUp()
    {
        $this->_areaListMock = $this->getMock('\Magento\Framework\App\AreaList', array(), array(), '', false);
        $this->_requestMock = $this->getMock('Magento\Framework\App\Request\Http', array(), array(), '', false);
        $this->appState = $this->getMock('Magento\Framework\App\State', array(), array(), '', false);
        $this->_model = new \Magento\View\DesignLoader(
            $this->_requestMock,
            $this->_areaListMock,
            $this->appState
        );
    }

    public function testLoad()
    {
        $area = $this->getMock('Magento\Core\Model\App\Area', array(), array(), '', false);
        $this->appState->expects($this->once())->method('getAreaCode')->will($this->returnValue('area'));
        $this->_areaListMock->expects($this->once())->method('getArea')->with('area')->will($this->returnValue($area));
        $area->expects($this->at(0))->method('load')
            ->with(\Magento\Core\Model\App\Area::PART_DESIGN)->will($this->returnValue($area));
        $area->expects($this->at(1))->method('load')
            ->with(\Magento\Core\Model\App\Area::PART_TRANSLATE)->will($this->returnValue($area));
        $this->_model->load($this->_requestMock);
    }
}
