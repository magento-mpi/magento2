<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Indexer\App;

class ShellTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Indexer\App\Shell
     */
    protected $entryPoint;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $shellFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $responseMock;

    protected function setUp()
    {
        $this->shellFactoryMock = $this->getMock(
            'Magento\Indexer\Model\ShellFactory',
            array('create'),
            array(),
            '',
            false
        );
        $this->responseMock = $this->getMock('Magento\Framework\App\Console\Response', array(), array(), '', false);
        $this->entryPoint = new \Magento\Indexer\App\Shell(
            'indexer.php',
            $this->shellFactoryMock,
            $this->responseMock
        );
    }

    /**
     * @param boolean $shellHasErrors
     * @dataProvider processRequestDataProvider
     */
    public function testProcessRequest($shellHasErrors)
    {
        $shell = $this->getMock('Magento\Indexer\Model\Shell', array(), array(), '', false);
        $shell->expects($this->once())->method('hasErrors')->will($this->returnValue($shellHasErrors));
        $shell->expects($this->once())->method('run');
        $this->shellFactoryMock->expects($this->any())->method('create')->will($this->returnValue($shell));

        $this->entryPoint->launch();
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
        $this->assertFalse($this->entryPoint->catchException($bootstrap, new \Exception));
    }
}
