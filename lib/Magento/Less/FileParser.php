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
class FileParser
{
    /**
     * @var \Magento\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\View\FileSystem
     */
    protected $viewFileSystem;

    /**
     * @var Instruction\ImportFactory
     */
    protected $importFactory;

    /**
     * @param \Magento\Filesystem $filesystem
     * @param \Magento\View\FileSystem $viewFileSystem
     * @param Instruction\ImportFactory $importFactory
     */
    public function __construct(
        \Magento\Filesystem $filesystem,
        \Magento\View\FileSystem $viewFileSystem,
        \Magento\Less\Instruction\ImportFactory $importFactory
    ) {
        $this->filesystem = $filesystem;
        $this->viewFileSystem = $viewFileSystem;
        $this->importFactory = $importFactory;
    }

    /**
     * @param string $content
     *
     * @return \Magento\Less\Instruction\Import[]
     */
    public function parse($content)
    {
        $imports = array();
        foreach ($this->_parse($content) as $row) {
            $isMagentoImport = $row[0] == \Magento\Less\Instruction\Import::TYPE_MAGENTO;
            $this->importFactory->create($row[1], $isMagentoImport);
        }
        return $imports;
    }

    /**
     * @param string $content
     * @return array
     */
    protected function _parse($content)
    {
        $instructions = array();
        $lines = explode(PHP_EOL, $content);
        foreach ($lines as $line) {
            $pattern = sprintf('(%s|%s)\s(.+)',
                preg_quote(\Magento\Less\Instruction\Import::TYPE_LESS),
                preg_quote(\Magento\Less\Instruction\Import::TYPE_MAGENTO)
            );
            $matches = null;
            if (preg_match($pattern, $line, $matches)) {
                $instruction = array_shift($matches);
                $instructions[] = $instruction;
            }
        }

        return array();
    }
}
