<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Less;

/**
 * File resolver for LESS import instructions
 */
class FileResolver
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
     * @param \Magento\Filesystem $filesystem
     * @param \Magento\View\FileSystem $viewFileSystem
     */
    public function __construct(
        \Magento\Filesystem $filesystem,
        \Magento\View\FileSystem $viewFileSystem
    ) {
        $this->filesystem = $filesystem;
        $this->viewFileSystem = $viewFileSystem;
    }

    public function get()
    {
    }
}
