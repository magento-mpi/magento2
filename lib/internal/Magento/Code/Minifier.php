<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Code;

use Magento\Filesystem\Directory\Read;

class Minifier
{
    /**
     * @var \Magento\Code\Minifier\StrategyInterface
     */
    private $_strategy;

    /**
     * @var Read
     */
    private $rootDirectory;

    /**
     * @var string Directory name where minified files are saved, relative to static view directory
     */
    private $targetDirRelView;

    /**
     * @var Read
     */
    private $staticViewDir;

    /**
     * @param \Magento\Code\Minifier\StrategyInterface $strategy
     * @param \Magento\App\Filesystem $filesystem
     * @param string $targetDirRelView
     */
    public function __construct(
        \Magento\Code\Minifier\StrategyInterface $strategy,
        \Magento\App\Filesystem $filesystem,
        $targetDirRelView
    ) {
        $this->_strategy = $strategy;
        $this->rootDirectory = $filesystem->getDirectoryRead(\Magento\App\Filesystem::ROOT_DIR);
        $this->staticViewDir = $filesystem->getDirectoryRead(\Magento\App\Filesystem::STATIC_VIEW_DIR);
        $this->targetDirRelView = $targetDirRelView;
    }

    /**
     * Get path to minified file
     *
     * @param string $originalFile
     * @return bool|string
     */
    public function getMinifiedFile($originalFile)
    {
        // Already minified
        if ($this->_isFileMinified($originalFile)) {
            return $originalFile;
        }

        // Has .min file in the same directory
        $originalFileRelRoot = $this->rootDirectory->getRelativePath($originalFile);
        $minifiedFileRelRoot = $this->_findOriginalMinifiedFile($originalFileRelRoot);
        if ($minifiedFileRelRoot) {
            return $this->rootDirectory->getAbsolutePath($minifiedFileRelRoot);
        }

        // Minify the file
        $minifiedFileRelView = $this->targetDirRelView . '/' . $this->_generateMinifiedFileName($originalFile);
        $this->_strategy->minifyFile($originalFileRelRoot, $minifiedFileRelView);
        return $this->staticViewDir->getAbsolutePath($minifiedFileRelView);
    }

    /**
     * Check if file is minified
     *
     * @param string $fileName
     * @return bool
     */
    protected function _isFileMinified($fileName)
    {
        return (bool)preg_match('#.min.\w+$#', $fileName);
    }

    /**
     * Generate name of the minified file
     *
     * @param string $originalFile
     * @return string
     */
    protected function _generateMinifiedFileName($originalFile)
    {
        $fileInfo = pathinfo($originalFile);
        $minifiedName = md5($originalFile) . '_' . $fileInfo['filename'] . '.min.' . $fileInfo['extension'];

        return $minifiedName;
    }

    /**
     * Search for minified file provided along with the original file in the code base
     *
     * @param string $originalFile
     * @return bool|string
     */
    protected function _findOriginalMinifiedFile($originalFile)
    {
        $fileInfo = pathinfo($originalFile);
        $minifiedFile = $fileInfo['dirname'] . '/' . $fileInfo['filename'] . '.min.' . $fileInfo['extension'];
        if ($this->rootDirectory->isExist($minifiedFile)) {
            return $minifiedFile;
        }
        return false;
    }
}
