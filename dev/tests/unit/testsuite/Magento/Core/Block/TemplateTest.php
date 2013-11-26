<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Block;

class TemplateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Block\Template
     */
    protected $_block;

    /**
     * @var \Magento\Filesystem|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_filesystem;

    /**
     * @var \Magento\View\TemplateEngineInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_templateEngine;

    /**
     * @var \Magento\View\FileSystem|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_viewFileSystem;

    protected function setUp()
    {
        $dirMap = array(
            array(\Magento\Filesystem::ROOT, 'base'),
            array(\Magento\Filesystem::APP, __DIR__),
            array(\Magento\Filesystem::THEMES, __DIR__ . '/design'),
        );
        $dirs = $this->getMock('Magento\App\Dir', array(), array(), '', false, false);
        $dirs->expects($this->never())->method('getDir');

        $this->_viewFileSystem = $this->getMock('\Magento\View\FileSystem', array(), array(), '', false);

        $this->_filesystem = $this->getMock('\Magento\Filesystem', array(), array(), '', false);
        $this->_filesystem->expects($this->any())->method('getPath')->will($this->returnValueMap($dirMap));

        $this->_templateEngine = $this->getMock('\Magento\View\TemplateEngineInterface');

        $enginePool = $this->getMock('Magento\View\TemplateEngineFactory', array(), array(), '', false);
        $enginePool->expects($this->any())
            ->method('get')
            ->with('phtml')
            ->will($this->returnValue($this->_templateEngine));

        $appState = $this->getMock('Magento\App\State', array('getAreaCode'), array(), '', false);
        $appState->expects($this->any())->method('getAreaCode')->will($this->returnValue('frontend'));

        $storeConfig = $this->getMock('Magento\Core\Model\Store\Config', array(), array(), '', false);
        $storeConfig->expects($this->any())->method('getConfigFlag')->will($this->returnValue(true));

        $context = $this->getMock('Magento\Core\Block\Template\Context', array(), array(), '', false);
        $context->expects($this->any())->method('getEngineFactory')->will($this->returnValue($enginePool));
        $context->expects($this->any())->method('getDirs')->will($this->returnValue($dirs));
        $context->expects($this->any())->method('getFilesystem')->will($this->returnValue($this->_filesystem));
        $context->expects($this->any())->method('getViewFileSystem')->will($this->returnValue($this->_viewFileSystem));
        $context->expects($this->any())->method('getAppState')->will($this->returnValue($appState));
        $context->expects($this->any())->method('getStoreConfig')->will($this->returnValue($storeConfig));
        $this->_block = new \Magento\Core\Block\Template(
            $this->getMock('\Magento\Core\Helper\Data', array(), array(), '', false),
            $context,
            array('template' => 'template.phtml', 'module_name' => 'Fixture_Module')
        );
    }

    public function testGetTemplateFile()
    {
        $params = array('module' => 'Fixture_Module', 'area' => 'frontend');
        $this->_viewFileSystem->expects($this->once())->method('getFilename')->with('template.phtml', $params);
        $this->_block->getTemplateFile();
    }

    public function testFetchView()
    {
        $this->expectOutputString('');

        $directoryMock = $this->getMock('Magento\Filesystem\Directory\Read', array(), array(), '', false);
        $directoryMock->expects($this->any())->method('isFile')->will($this->returnValue(true));
        $this->_filesystem
            ->expects($this->once())->method('getDirectoryRead')->with('base')->will($this->returnValue($directoryMock));

        $output = '<h1>Template Contents</h1>';
        $vars = array('var1' => 'value1', 'var2' => 'value2');
        $this->_templateEngine
            ->expects($this->once())
            ->method('render')
            ->with($this->identicalTo($this->_block), 'template.phtml', $vars)
            ->will($this->returnValue($output))
        ;
        $this->_block->assign($vars);
        $this->assertEquals($output, $this->_block->fetchView('template.phtml'));
    }
}
