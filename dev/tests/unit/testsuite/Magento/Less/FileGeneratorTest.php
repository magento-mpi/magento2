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
     * @var \Magento\View\Asset\Repository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $assetRepo;

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
        $this->assetRepo = $this->getMock('\Magento\View\Asset\Repository', array(), array(), '', false);
        $this->magentoImport = $this->getMock(
            '\Magento\Less\PreProcessor\Instruction\MagentoImport', array(), array(), '', false
        );
        $this->import = $this->getMock('\Magento\Less\PreProcessor\Instruction\Import', array(), array(), '', false);
        $this->object = new \Magento\Less\FileGenerator(
            $filesystem, $this->assetRepo, $this->magentoImport, $this->import
        );
    }

    public function testGenerateLessFileTree()
    {
        $originalContent = 'original content';
        $expectedContent = 'updated content';
        $expectedRelativePath = 'view_preprocessed/less/some/file.less';
        $expectedPath = '/var/view_preprocessed/less/some/file.less';

        $asset = $this->getMock('\Magento\View\Asset\File', array(), array(), '', false);
        $asset->expects($this->once())
            ->method('getPath')
            ->will($this->returnValue('some/file.css'));
        $chain = new \Magento\View\Asset\PreProcessor\Chain($asset, $originalContent, 'less');

        $this->magentoImport->expects($this->once())
            ->method('process')
            ->with($chain)
        ;
        $this->import->expects($this->once())
            ->method('process')
            ->with($chain)
        ;

        $relatedAssetOne = $this->getMock('\Magento\View\Asset\File', array(), array(), '', false);
        $relatedAssetOne->expects($this->any())
            ->method('getPath')
            ->will($this->returnValue('related/file_one.css'));
        $relatedAssetOne->expects($this->any())
            ->method('getContent')
            ->will($this->returnValue("content of 'related/file_one.css'"));
        $relatedAssetTwo = $this->getMock('\Magento\View\Asset\File', array(), array(), '', false);
        $relatedAssetTwo->expects($this->any())
            ->method('getPath')
            ->will($this->returnValue('related/file_two.css'));
        $relatedAssetTwo->expects($this->any())
            ->method('getContent')
            ->will($this->returnValue("content of 'related/file_two.css'"));
        $assetsMap = [
            ['related/file_one.css', $asset, $relatedAssetOne],
            ['related/file_two.css', $asset, $relatedAssetTwo],
        ];
        $this->assetRepo->expects($this->any())
            ->method('createRelated')
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

        $actual = $this->object->generateLessFileTree($chain);
        $this->assertSame($expectedPath, $actual);
    }
}
