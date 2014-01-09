<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Index
 * @subpackage  unit_tests
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
    protected $_shellErrorHandler;

    protected function setUp()
    {
        $this->_shellFactory = $this->getMock('Magento\Index\Model\ShellFactory', array('create'), array(), '', false);
        $this->_shellErrorHandler = $this->getMock(
            'Magento\Index\App\Shell\ErrorHandler',
            array(),
            array(),
            '',
            false
        );
        $this->_entryPoint = new \Magento\Index\App\Shell(
            'indexer.php',
            $this->_shellFactory,
            $this->_shellErrorHandler
        );
    }

    /**
     * @param boolean $shellHasErrors
     * @dataProvider processRequestDataProvider
     */
    public function testProcessRequest($shellHasErrors)
    {
        $shell = $this->getMock('Magento\Index\Model\Shell', array(), array(), '', false);
        $shell->expects($this->once())
            ->method('hasErrors')
            ->will($this->returnValue($shellHasErrors));
        $shell->expects($this->once())
            ->method('run');
        if ($shellHasErrors) {
            $this->_shellErrorHandler->expects($this->once())
                ->method('terminate')
                ->with(1);
        }
        $this->_shellFactory->expects($this->any())
            ->method('create')
            ->will($this->returnValue($shell)
            );

        $this->_entryPoint->launch();
    }

    /**
     * @return array
     */
    public function processRequestDataProvider()
    {
        return array(
            array(true),
            array(false)
        );
    }
}
