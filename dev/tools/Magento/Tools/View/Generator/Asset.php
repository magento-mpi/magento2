<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Tools\View\Generator;

/**
 * A workaround for assets to suppress context of locale and file resolution
 *
 * The Generator tool currently doesn't implement support of locale, as well as it doesn't use getSourceFile() feature
 */
class Asset extends \Magento\View\Asset\FileId
{
    /**
     * @param \Magento\View\Asset\PathGenerator $pathGenerator
     * @param string $fileId
     * @param string $area
     * @param string $themePath
     */
    public function __construct(\Magento\View\Asset\PathGenerator $pathGenerator, $fileId, $area, $themePath)
    {
        $this->pathGenerator = $pathGenerator;
        $this->fileSource = null;
        list($this->module, $filePath) = parent::extractModule($fileId);
        $this->area = $area;
        $this->themePath = $themePath;
        \Magento\View\Asset\File::__construct($filePath, null, null);
    }

    /**
     * Create a fake File ID asset that can only resolve relative path
     *
     * @param string $fileId
     * @return Asset
     */
    public function createRelative($fileId)
    {
        return new self($this->pathGenerator, $fileId, $this->area, $this->themePath);
    }

    /**
     * @inheritdoc
     */
    public function getRelativePath()
    {
        $result = parent::getRelativePath();
        return str_replace('//', '/', $result); // workaround for missing locale code
    }
}
