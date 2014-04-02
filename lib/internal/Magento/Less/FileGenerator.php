<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Less;

use Magento\View\Asset\LocalInterface;
use Magento\View\Asset\File\Source;

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
     * @var \Magento\View\Asset\File\Source
     */
    private $assetSource;

    /**
     * @var \Magento\View\Asset\Repository
     */
    private $assetRepo;

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
     * @param \Magento\View\Asset\File\Source $assetSource
     * @param \Magento\View\Asset\Repository $assetRepo
     * @param PreProcessor\Instruction\MagentoImport $magentoImportProcessor
     * @param PreProcessor\Instruction\Import $importProcessor
     */
    public function __construct(
        \Magento\App\Filesystem $filesystem,
        \Magento\View\Asset\File\Source $assetSource,
        \Magento\View\Asset\Repository $assetRepo,
        \Magento\Less\PreProcessor\Instruction\MagentoImport $magentoImportProcessor,
        \Magento\Less\PreProcessor\Instruction\Import $importProcessor
    ) {
        $this->tmpDirectory = $filesystem->getDirectoryWrite(\Magento\App\Filesystem::VAR_DIR);
        $this->rootDirectory = $filesystem->getDirectoryRead(\Magento\App\Filesystem::ROOT_DIR);
        $this->assetSource = $assetSource;
        $this->assetRepo = $assetRepo;
        $this->magentoImportProcessor = $magentoImportProcessor;
        $this->importProcessor = $importProcessor;
    }

    /**
     * Create a tree of self-sustainable files and return the topmost LESS file, ready for passing to 3rd party library
     *
     * @param string $lessContent
     * @param LocalInterface $cssFileAsset
     * @return string Absolute path of generated LESS file
     */
    public function generateLessFileTree($lessContent, LocalInterface $cssFileAsset)
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
     * @param LocalInterface $asset
     */
    protected function generateRelatedFile($relatedFileId, LocalInterface $asset)
    {
        $relatedAsset = $this->assetRepo->createRelative($relatedFileId, $asset);
        $resolvedPath = $this->assetSource->getFile($relatedAsset); // indirect recursion
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
        $filePath = Source::TMP_MATERIALIZATION_DIR . '/' . self::TMP_LESS_DIR . '/' . $relativePath;
        $this->tmpDirectory->writeFile($filePath, $contents);
        return $this->tmpDirectory->getAbsolutePath($filePath);
    }
}
