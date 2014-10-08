<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\View\Design\FileResolution\Fallback\Resolver;

use Magento\Framework\App\Filesystem\DirectoryList;

class AlternativeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Filesystem\Directory\Read|\PHPUnit_Framework_MockObject_MockObject
     */
    private $directory;

    /**
     * @var \Magento\Framework\View\Design\Fallback\Rule\RuleInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $rule;

    /**
     * @var \Magento\Framework\View\Design\FileResolution\Fallback\Resolver\Simple
     */
    private $object;

    protected function setUp()
    {
        $this->directory = $this->getMock('\Magento\Framework\Filesystem\Directory\Read', array(), array(), '', false);
        $this->directory->expects($this->any())
            ->method('getRelativePath')
            ->will($this->returnArgument(0));
        $filesystem = $this->getMock('\Magento\Framework\App\Filesystem', array(), array(), '', false);
        $filesystem->expects($this->once())
            ->method('getDirectoryRead')
            ->with(DirectoryList::ROOT)
            ->will($this->returnValue($this->directory));
        $this->rule = $this->getMock(
            '\Magento\Framework\View\Design\Fallback\Rule\RuleInterface', array(), array(), '', false
        );
        $rulePool = $this->getMock('Magento\Framework\View\Design\Fallback\RulePool', array(), array(), '', false);
        $rulePool->expects($this->any())
            ->method('getRule')
            ->with('type')
            ->will($this->returnValue($this->rule));
        $cache = $this->getMockForAbstractClass(
            'Magento\Framework\View\Design\FileResolution\Fallback\CacheDataInterface'
        );
        $cache->expects($this->any())
            ->method('getFromCache')
            ->will($this->returnValue(false));
        $this->object = new Alternative($filesystem, $rulePool, $cache, ['css' => ['less']]);
    }

    /**
     * @param array $alternativeExtensions
     *
     * @dataProvider constructorExceptionDataProvider
     */
    public function testConstructorException(array $alternativeExtensions)
    {
        $this->setExpectedException('\InvalidArgumentException', "\$alternativeExtensions must be an array with format:"
            . " array('ext1' => array('ext1', 'ext2'), 'ext3' => array(...)]");

        $filesystem = $this->getMock('Magento\Framework\App\Filesystem', array(), array(), '', false);
        $rulePool = $this->getMock('Magento\Framework\View\Design\Fallback\RulePool', array(), array(), '', false);
        $cache = $this->getMockForAbstractClass(
            'Magento\Framework\View\Design\FileResolution\Fallback\CacheDataInterface'
        );
        new Alternative($filesystem, $rulePool, $cache, $alternativeExtensions);
    }

    /**
     * @return array
     */
    public function constructorExceptionDataProvider()
    {
        return [
            'numerical keys'   => [['css', 'less']],
            'non-array values' => [['css' => 'less']],
        ];
    }

    public function testResolve()
    {
        $requestedFile = 'file.css';
        $expected = 'some/dir/file.less';

        $theme = $this->getMockForAbstractClass('Magento\Framework\View\Design\ThemeInterface');
        $theme->expects($this->any())
            ->method('getFullPath')
            ->will($this->returnValue('magento_theme'));
        $this->rule->expects($this->atLeastOnce())
            ->method('getPatternDirs')
            ->will($this->returnValue(['some/dir']));

        $fileExistsMap = [
            ['some/dir/file.css', false],
            ['some/dir/file.less', true],
        ];
        $this->directory->expects($this->any())
            ->method('isExist')
            ->will($this->returnValueMap($fileExistsMap));

        $actual = $this->object->resolve('type', $requestedFile, 'frontend', $theme, 'en_US', 'Magento_Module');
        $this->assertSame($expected, $actual);
    }
}
