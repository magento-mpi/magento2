<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Less;

use Magento\View\Asset\FileId;

class FileGenerator
{
    /**
     * Temporary directory prefix
     */
    const TMP_LESS_DIR = 'less';

    /**
     * @var \Magento\Filesystem\Directory\WriteInterface
     */
    protected $tmpDirectory;

    /**
     * @var \Magento\Filesystem\Directory\ReadInterface
     */
    protected $rootDirectory;

    /**
     * @var \Magento\View\Asset\FileId\Source
     */
    private $viewService;

    /**
     * @var \Magento\Less\PreProcessor\Instruction\MagentoImport
     */
    private $magentoImportProcessor;

    /**
     * @var \Magento\Less\PreProcessor\Instruction\Import
     */
    private $importProcessor;

    /**
     * @param \Magento\App\Filesystem $filesystem
     * @param \Magento\View\Asset\FileId\Source $assetSource
     * @param PreProcessor\Instruction\MagentoImport $magentoImportProcessor
     * @param PreProcessor\Instruction\Import $importProcessor
     */
    public function __construct(
        \Magento\App\Filesystem $filesystem,
        \Magento\View\Asset\FileId\Source $assetSource,
        \Magento\Less\PreProcessor\Instruction\MagentoImport $magentoImportProcessor,
        \Magento\Less\PreProcessor\Instruction\Import $importProcessor
    ) {
        $this->tmpDirectory = $filesystem->getDirectoryWrite(\Magento\App\Filesystem::VAR_DIR);
        $this->rootDirectory = $filesystem->getDirectoryRead(\Magento\App\Filesystem::ROOT_DIR);
        $this->viewService = $assetSource;
        $this->magentoImportProcessor = $magentoImportProcessor;
        $this->importProcessor = $importProcessor;
    }

    /**
     * Create a tree of self-sustainable files and return the topmost LESS file, ready for passing to 3rd party library
     *
     * @param string $lessContent
     * @param FileId $cssFileAsset
     * @return string Absolute path of generated LESS file
     */
    public function generateLessFileTree($lessContent, FileId $cssFileAsset)
    {
        list($lessContent) = $this->magentoImportProcessor->process($lessContent, 'less', $cssFileAsset);

        list($lessContent) = $this->importProcessor->process($lessContent, 'less', $cssFileAsset);
        $this->generateRelatedFiles();

        $lessRelativePath = preg_replace('#\.css$#', '.less', $cssFileAsset->getRelativePath());
        $tmpFilePath = $this->createFile($lessRelativePath, $lessContent);
        return $tmpFilePath;
    }

    /**
     * Create all asset files, referenced from already processed ones
     */
    protected function generateRelatedFiles()
    {
        do {
            $relatedFiles = $this->importProcessor->getRelatedFiles();
            $this->importProcessor->resetRelatedFiles();
            foreach ($relatedFiles as $relatedFileInfo) {
                list($relatedFileId, $asset) = $relatedFileInfo;
                $this->generateRelatedFile($relatedFileId, $asset);
            }
        } while ($relatedFiles);
    }

    /**
     * Create file, referenced relatively to an asset
     *
     * @param string $relatedFileId
     * @param FileId $asset
     */
    protected function generateRelatedFile($relatedFileId, FileId $asset)
    {
        $relatedAsset = $asset->createRelative($relatedFileId);
        $resolvedPath = $this->viewService->getSourceFile($relatedAsset); // indirect recursion
        $contents = $this->rootDirectory->readFile($this->rootDirectory->getRelativePath($resolvedPath));
        $this->createFile($relatedAsset->getRelativePath(), $contents);
    }

    /**
     * Write down contents to a temporary file and return its absolute path
     *
     * @param string $relativePath
     * @param string $contents
     * @return string
     */
    protected function createFile($relativePath, $contents)
    {
        $filePath = FileId\Source::TMP_MATERIALIZATION_DIR . '/' . self::TMP_LESS_DIR . '/' . $relativePath;
        $this->tmpDirectory->writeFile($filePath, $contents);
        return $this->tmpDirectory->getAbsolutePath($filePath);
    }
}
