<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Publisher;

/**
 * Basic publisher file type
 */
class File implements FileInterface
{
    /**
     * @var string
     */
    protected $filePath;

    /**
     * @var string
     */
    protected $extension;

    /**
     * @var array
     */
    protected $viewParams;

    /**
     * @var null|string
     */
    protected $sourcePath;

    /**
     * @var \Magento\App\Filesystem
     */
    protected $filesystem;

    /**
     * @param \Magento\App\Filesystem $filesystem
     * @param string $filePath
     * @param string $extension
     * @param array $viewParams
     */
    public function __construct(
        \Magento\App\Filesystem $filesystem,
        $filePath,
        $extension,
        array $viewParams
    ) {
        $this->filesystem = $filesystem;
        $this->filePath = $filePath;
        $this->extension = $extension;
        $this->viewParams = $viewParams;
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @return array
     */
    public function getViewParams()
    {
        return $this->viewParams;
    }

    /**
     * Determine whether a file needs to be published
     *
     * All files located in 'pub/lib' dir should not be published cause it's already publicly accessible.
     * All other files must be processed either if they are not published already (located in 'pub/static'),
     * or if they are css-files and we're working in developer mode.
     *
     * If sourcePath points to file in 'pub/lib' dir - no publishing required
     * If sourcePath points to file in 'pub/static' dir - no publishing required
     *
     * @return bool
     */
    public function isPublicationAllowed()
    {
        $filePath = str_replace('\\', '/', $this->filePath);

        $pubLibDir = $this->filesystem->getPath(\Magento\App\Filesystem::PUB_LIB_DIR) . '/';
        if (strncmp($filePath, $pubLibDir, strlen($pubLibDir)) === 0) {
            return false;
        }

        $pubStaticDir = $this->filesystem->getPath(\Magento\App\Filesystem::STATIC_VIEW_DIR) . '/';
        if (strncmp($filePath, $pubStaticDir, strlen($pubStaticDir)) !== 0) {
            return true;
        }
        return false;
    }

    /**
     * @param string $sourcePath
     * @return $this
     */
    public function setSourcePath($sourcePath)
    {
        $this->sourcePath = $sourcePath;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSourcePath()
    {
        return $this->sourcePath;
    }
}
