<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Less;

/**
 * LESS file parser
 */
class FileBuilder
{
    /**
     * @var \Magento\Filesystem
     */
    protected $filesystem;

    /**
     * @var Instruction\ImportFactory
     */
    protected $importFactory;

    /**
     * @param \Magento\Filesystem $filesystem
     * @param Instruction\ImportFactory $importFactory
     */
    public function __construct(
        \Magento\Filesystem $filesystem,
        \Magento\Less\Instruction\ImportFactory $importFactory
    ) {
        $this->filesystem = $filesystem;
        $this->importFactory = $importFactory;
    }

    /**
     * @param string $filePath
     * @param \Magento\Less\Instruction\Import[] $instructions
     * @return $this
     */
    public function build($filePath, $instructions)
    {
        $content = $this->_build($instructions);
        $this->filesystem->getDirectoryWrite(dirname($filePath))->writeFile($filePath, $content);

        return $filePath;
    }

    /**
     * @param \Magento\Less\Instruction\Import[] $instructions
     * @return string
     */
    protected function _build($instructions)
    {
        $lines = array();
        foreach ($instructions as $instruction) {
            $lines[] = $instruction->render();
        }
        return join(PHP_EOL, $lines);
    }
}
