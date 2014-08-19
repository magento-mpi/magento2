<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Index\App;

class ShellTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Index\App\Shell
     */
    protected $_entryPoint;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_shellFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_responseMock;

    protected function setUp()
    {
        $this->_shellFactory = $this->getMock('Magento\Index\Model\ShellFactory', array('create'), array(), '', false);
        $this->_responseMock = $this->getMock('Magento\Framework\App\Console\Response', array(), array(), '', false);
        $this->_entryPoint = new \Magento\Index\App\Shell('indexer.php', $this->_shellFactory, $this->_responseMock);
    }

    /**
     * @param boolean $shellHasErrors
     * @dataProvider processRequestDataProvider
     */
    public function testProcessRequest($shellHasErrors)
    {
        $shell = $this->getMock('Magento\Index\Model\Shell', array(), array(), '', false);
        $shell->expects($this->once())->method('hasErrors')->will($this->returnValue($shellHasErrors));
        $shell->expects($this->once())->method('run');
        if ($shellHasErrors) {
            $this->_responseMock->expects($this->once())->method('setCode')->with(-1);
        } else {
            $this->_responseMock->expects($this->once())->method('setCode')->with(0);
        }
        $this->_shellFactory->expects($this->any())->method('create')->will($this->returnValue($shell));

        $this->_entryPoint->launch();
    }

    /**
     * @return array
     */
    public function processRequestDataProvider()
    {
        return array(array(true), array(false));
    }

    public function testCatchException()
    {
        $bootstrap = $this->getMock('Magento\Framework\App\Bootstrap', array(), array(), '', false);
        $this->assertFalse($this->_entryPoint->catchException($bootstrap, new \Exception));
    }
}
