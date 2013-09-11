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

class Magento_Core_Block_TemplateTest extends PHPUnit_Framework_TestCase
{
    public function testGetTemplateFile()
    {
        $template = 'fixture';
        $area = 'areaFixture';
        $params = array('module' => 'Magento_Core', 'area' => $area);

        $fileSystem = $this->getMock('Magento\Core\Model\View\FileSystem', array(), array(), '', false);
        $fileSystem->expects($this->once())->method('getFilename')->with($template, $params);
        $arguments = array(
            'viewFileSystem' => $fileSystem,
            'data'           => array('template' => $template, 'area' => $area),
        );
        $helper = new Magento_TestFramework_Helper_ObjectManager($this);

        $block = $helper->getObject('\Magento\Core\Block\Template', $arguments);

        $block->getTemplateFile();
    }

    /**
     * @param string $filename
     * @param string $expectedOutput
     * @dataProvider fetchViewDataProvider
     */
    public function testFetchView($filename, $expectedOutput)
    {
        $map = array(
            array(\Magento\Core\Model\Dir::APP, __DIR__),
            array(\Magento\Core\Model\Dir::THEMES, __DIR__ . 'design'),
        );
        $dirMock = $this->getMock('Magento\Core\Model\Dir', array(), array(), '', false, false);
        $dirMock->expects($this->any())->method('getDir')->will($this->returnValueMap($map));
        $layout = $this->getMock('Magento\Core\Model\Layout', array('isDirectOutput'), array(), '', false);
        $filesystem = new \Magento\Filesystem(new \Magento\Filesystem\Adapter\Local);
        $design = $this->getMock('Magento\Core\Model\View\DesignInterface', array(), array(), '', false);
        $translator = $this->getMock('Magento\Core\Model\Translate', array(), array(), '', false);

        $objectManagerMock = $this->getMock('Magento\ObjectManager', array('get', 'create', 'configure'));
        $objectManagerMock->expects($this->any())
            ->method('get')
            ->with('Magento\Core\Model\TemplateEngine\Php')
            ->will($this->returnValue(new \Magento\Core\Model\TemplateEngine\Php()));
        $engineFactory = new \Magento\Core\Model\TemplateEngine\Factory($objectManagerMock);

        $arguments = array(
            'design'        => $design,
            'layout'        => $layout,
            'dirs'          => $dirMock,
            'filesystem'    => $filesystem,
            'translator'    => $translator,
            'engineFactory' => $engineFactory,
        );
        $helper = new Magento_TestFramework_Helper_ObjectManager($this);

        $block = $this->getMock(
            '\Magento\Core\Block\Template',
            array('getShowTemplateHints'),
            $helper->getConstructArguments('\Magento\Core\Block\Template', $arguments)
        );
        $layout->expects($this->once())->method('isDirectOutput')->will($this->returnValue(false));

        $this->assertSame($block, $block->assign(array('varOne' => 'value1', 'varTwo' => 'value2')));
        $this->assertEquals($expectedOutput, $block->fetchView(__DIR__ . "/_files/{$filename}"));
    }

    /**
     * @return array
     */
    public function fetchViewDataProvider()
    {
        return array(
            array('template_test_assign.phtml', 'value1, value2'),
            array('invalid_file', ''),
        );
    }
}
