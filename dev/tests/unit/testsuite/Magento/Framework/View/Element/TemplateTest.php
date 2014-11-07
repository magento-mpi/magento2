<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Element;

class TemplateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\View\Element\Template
     */
    protected $_block;

    /**
     * @var \Magento\Framework\Filesystem|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_filesystem;

    /**
     * @var \Magento\Framework\View\TemplateEngineInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_templateEngine;

    /**
     * @var \Magento\Framework\View\FileSystem|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_viewFileSystem;

    /**
     * @var \Magento\Framework\Filesystem\Directory\Read|\PHPUnit_Framework_MockObject_MockObject
     */
    private $rootDirMock;

    protected function setUp()
    {
        $this->_viewFileSystem = $this->getMock('\Magento\Framework\View\FileSystem', array(), array(), '', false);

        $this->rootDirMock = $this->getMock('Magento\Framework\Filesystem\Directory\Read', array(), array(), '', false);
        $this->rootDirMock->expects($this->any())
            ->method('getRelativePath')
            ->will($this->returnArgument(0))
        ;
        $appDirMock = $this->getMock('\Magento\Framework\Filesystem\Directory\Read', array(), array(), '', false);
        $themesDirMock = $this->getMock('\Magento\Framework\Filesystem\Directory\Read', array(), array(), '', false);
        $themesDirMock->expects($this->any())
            ->method('getAbsolutePath')
            ->will($this->returnValue('themedir'));

        $this->_filesystem = $this->getMock('\Magento\Framework\Filesystem', array(), array(), '', false);
        $this->_filesystem->expects($this->any())
            ->method('getDirectoryRead')
            ->will($this->returnValueMap([
                [\Magento\Framework\App\Filesystem\DirectoryList::THEMES, $themesDirMock],
                [\Magento\Framework\App\Filesystem\DirectoryList::APP, $appDirMock],
                [\Magento\Framework\App\Filesystem\DirectoryList::ROOT, $this->rootDirMock],
            ]))
        ;

        $this->_templateEngine = $this->getMock(
            'Magento\Framework\View\TemplateEnginePool',
            array('render', 'get'),
            array(),
            '',
            false
        );

        $this->_templateEngine->expects($this->any())->method('get')->will($this->returnValue($this->_templateEngine));

        $appState = $this->getMock('Magento\Framework\App\State', array('getAreaCode'), array(), '', false);
        $appState->expects($this->any())->method('getAreaCode')->will($this->returnValue('frontend'));
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_block = $helper->getObject(
            'Magento\Framework\View\Element\Template',
            array(
                'filesystem' => $this->_filesystem,
                'enginePool' => $this->_templateEngine,
                'viewFileSystem' => $this->_viewFileSystem,
                'appState' => $appState,
                'data' => array('template' => 'template.phtml', 'module_name' => 'Fixture_Module')
            )
        );
    }

    public function testGetTemplateFile()
    {
        $params = array('module' => 'Fixture_Module', 'area' => 'frontend');
        $this->_viewFileSystem->expects($this->once())->method('getTemplateFileName')->with('template.phtml', $params);
        $this->_block->getTemplateFile();
    }

    public function testFetchView()
    {
        $this->expectOutputString('');
        $template = 'themedir/template.phtml';
        $this->rootDirMock->expects($this->once())
            ->method('isFile')
            ->with($template)
            ->will($this->returnValue(true))
        ;

        $output = '<h1>Template Contents</h1>';
        $vars = array('var1' => 'value1', 'var2' => 'value2');
        $this->_templateEngine->expects($this->once())->method('render')->will($this->returnValue($output));
        $this->_block->assign($vars);
        $this->assertEquals($output, $this->_block->fetchView($template));
    }

    public function testSetTemplateContext()
    {
        $template = 'themedir/template.phtml';
        $this->rootDirMock->expects($this->once())
            ->method('isFile')
            ->with($template)
            ->will($this->returnValue(true))
        ;
        $context = new \Magento\Framework\Object();
        $this->_templateEngine->expects($this->once())->method('render')->with($context);
        $this->_block->setTemplateContext($context);
        $this->_block->fetchView($template);
    }
}
