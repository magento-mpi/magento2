<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Css\Model\PreProcessor;

use Magento\View\Asset;

class Less implements PreProcessorInterface
{
    /**
     * @var \Magento\View\FileSystem
     */
    protected $viewFileSystem;

    /**
     * @var \Magento\Module\View\FileResolver $viewFileResolver
     */
    protected $viewFileResolver;

    /**
     * @var \Magento\Css\PreProcessor\AdapterInterface
     */
    protected $adapter;

    /**
     * @var \Magento\Less\FileParser
     */
    protected $parser;

    /**
     * @var \Magento\Less\FileBuilder
     */
    protected $builder;

    /**
     * @var \Magento\Less\Instruction\ImportFactory
     */
    protected $importFactory;

    public function __construct(
        \Magento\View\FileSystem $viewFileSystem,
        \Magento\Less\FileResolver $viewFileResolver,
        \Magento\Css\PreProcessor\AdapterInterface $adapter,
        \Magento\Less\FileParser $parser,
        \Magento\Less\FileBuilder $builder,
        \Magento\Less\Instruction\ImportFactory $importFactory
    ) {
        $this->viewFileSystem = $viewFileSystem;
        $this->viewFileResolver = $viewFileResolver;
        $this->adapter = $adapter;
        $this->parser = $parser;
        $this->importFactory = $importFactory;
        $this->builder = $builder;
    }

    /**
     * @param string $filePath
     * @param array $params
     * @param \Magento\Filesystem\Directory\WriteInterface $targetDirectory
     * @param null $sourcePath
     * @return null|string
     */
    public function process($filePath, $params, $targetDirectory, $sourcePath = null)
    {
        // if css file has being already discovered/prepared by previous pre-processor
        if ($sourcePath) {
            return $sourcePath;
        }

        $sourcePath = $this->viewFileSystem->getViewFile($filePath, $params);
        // if css file is already exist. May compare modification time of .less and .css files here.
        if ($sourcePath) {
            return $sourcePath;
        }

        $lessFilePath = str_replace('.css', '.less', $filePath);

        $lessFileSourcePath = $this->viewFileSystem->getViewFile($lessFilePath, $params);

        $preparedLessFileSourcePath = $this->prepareFinalLessFile($lessFileSourcePath);

        $cssContent = $this->adapter->process($preparedLessFileSourcePath);

        // doesn't matter where exact file has been found, we use original file identifier
        // see \Magento\View\Publisher::_buildPublishedFilePath() for details
        $targetDirectory->writeFile($filePath, $cssContent);

        return $targetDirectory->getAbsolutePath($filePath);
    }

    protected function prepareFinalLessFile($lessFileSourcePath)
    {
        //TODO: Concerns about order

        $importInstructions = $this->parser->parse(file_get_contents($lessFileSourcePath));
        $resolvedInstructions = array();
        foreach ($importInstructions as $instruction) {
            if ($instruction->isMagentoImport()) {
                $file = $this->viewFileResolver->get($instruction->getFile());
                $resolvedInstructions[] = $this->importFactory->create($file, false);
            } else {
                $files = $this->viewFileSystem->get($instruction->getFile());
                foreach ($files as $file) {
                    $resolvedInstructions[] = $this->importFactory->create($file, false);
                }
            }
        }

        //$filePath is the same as $lessFileSourcePath actually
        $filePath = $this->builder->build($lessFileSourcePath, $resolvedInstructions);

        return $filePath;
    }
}
