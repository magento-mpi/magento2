<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Less;

class FileGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Less\PreProcessor\Instruction\Import|\PHPUnit_Framework_MockObject_MockObject
     */
    private $import;

    /**
     * @var \Magento\Less\PreProcessor\Instruction\MagentoImport|\PHPUnit_Framework_MockObject_MockObject
     */
    private $magentoImport;

    /**
     * @var \Magento\Filesystem\Directory\WriteInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $tmpDirectory;

    /**
     * @var \Magento\Filesystem\Directory\ReadInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $rootDirectory;

    /**
     * @var \Magento\View\Asset\FileId\Source|\PHPUnit_Framework_MockObject_MockObject
     */
    private $assetSource;

    /**
     * @var \Magento\Less\FileGenerator
     */
    private $object;

    protected function setUp()
    {
        $this->tmpDirectory = $this->getMockForAbstractClass('\Magento\Filesystem\Directory\WriteInterface');
        $this->rootDirectory = $this->getMockForAbstractClass('\Magento\Filesystem\Directory\ReadInterface');
        $this->rootDirectory->expects($this->any())
            ->method('getRelativePath')
            ->will($this->returnArgument(0));
        $this->rootDirectory->expects($this->any())
            ->method('readFile')
            ->will($this->returnCallback(function ($file) {
                return "content of '$file'";
            }));
        $filesystem = $this->getMock('\Magento\App\Filesystem', array(), array(), '', false);
        $filesystem->expects($this->once())
            ->method('getDirectoryWrite')
            ->with(\Magento\App\Filesystem::VAR_DIR)
            ->will($this->returnValue($this->tmpDirectory));
        $filesystem->expects($this->once())
            ->method('getDirectoryRead')
            ->with(\Magento\App\Filesystem::ROOT_DIR)
            ->will($this->returnValue($this->rootDirectory));
        $this->assetSource = $this->getMock('\Magento\View\Asset\FileId\Source', array(), array(), '', false);
        $this->magentoImport = $this->getMock(
            '\Magento\Less\PreProcessor\Instruction\MagentoImport', array(), array(), '', false
        );
        $this->import = $this->getMock('\Magento\Less\PreProcessor\Instruction\Import', array(), array(), '', false);
        $this->object = new \Magento\Less\FileGenerator(
            $filesystem, $this->assetSource, $this->magentoImport, $this->import
        );
    }

    public function testGenerateLessFileTree()
    {
        $originalContent = 'original content';
        $magentoContent = 'magento content';
        $expectedContent = 'updated content';
        $expectedRelativePath = 'view_preprocessed/less/some/file.less';
        $expectedPath = '/var/view_preprocessed/less/some/file.less';

        $asset = $this->getMock('\Magento\View\Asset\FileId', array(), array(), '', false);
        $asset->expects($this->once())
            ->method('getRelativePath')
            ->will($this->returnValue('some/file.css'));

        $this->magentoImport->expects($this->once())
            ->method('process')
            ->with($originalContent, 'less', $asset)
            ->will($this->returnValue([$magentoContent]));
        $this->import->expects($this->once())
            ->method('process')
            ->with($magentoContent, 'less', $asset)
            ->will($this->returnValue([$expectedContent]));

        $relatedAssetOne = $this->getMock('\Magento\View\Asset\FileId', array(), array(), '', false);
        $relatedAssetOne->expects($this->any())
            ->method('getRelativePath')
            ->will($this->returnValue('related/file_one.css'));
        $relatedAssetTwo = $this->getMock('\Magento\View\Asset\FileId', array(), array(), '', false);
        $relatedAssetTwo->expects($this->any())
            ->method('getRelativePath')
            ->will($this->returnValue('related/file_two.css'));
        $assetsMap = [
            ['related/file_one.css', $relatedAssetOne],
            ['related/file_two.css', $relatedAssetTwo],
        ];
        $asset->expects($this->any())
            ->method('createRelative')
            ->will($this->returnValueMap($assetsMap));

        $relatedFilesOne = [['related/file_one.css', $asset]];
        $this->import->expects($this->at(1))
            ->method('getRelatedFiles')
            ->will($this->returnValue($relatedFilesOne));
        $relatedFilesTwo = [['related/file_two.css', $asset]];
        $this->import->expects($this->at(3))
            ->method('getRelatedFiles')
            ->will($this->returnValue($relatedFilesTwo));
        $this->import->expects($this->at(5))
            ->method('getRelatedFiles')
            ->will($this->returnValue([]));

        $this->assetSource->expects($this->exactly(2))
            ->method('getSourceFile')
            ->will($this->returnCallback(function (\Magento\View\Asset\FileId $asset) {
                return $asset->getRelativePath();
            }));

        $writeMap = [
            [$expectedRelativePath, $expectedContent],
            ['related/file_one.css', "content of 'related/file_one.css'"],
            ['related/file_two.css', "content of 'related/file_two.css'"],
        ];
        $pathsMap = [
            [$expectedRelativePath, $expectedPath],
            ['related/file_one.css', '/var/view_preprocessed/less/related/file_one.css'],
            ['related/file_two.css', '/var/view_preprocessed/less/related/file_two.css'],
        ];
        $this->tmpDirectory->expects($this->any())
            ->method('writeFile')
            ->will($this->returnValueMap($writeMap));
        $this->tmpDirectory->expects($this->any())
            ->method('getAbsolutePath')
            ->will($this->returnValueMap($pathsMap));

        $actual = $this->object->generateLessFileTree($originalContent, $asset);
        $this->assertSame($expectedPath, $actual);
    }
}
