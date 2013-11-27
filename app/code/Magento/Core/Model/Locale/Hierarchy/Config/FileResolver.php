<?php
/**
 * Hierarchy config file resolver
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Locale\Hierarchy\Config;

use \Magento\Filesystem;

class FileResolver implements \Magento\Config\FileResolverInterface
{

    /**
     * Filesystem instance
     *
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @param \Magento\Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @inheritdoc
     */
    public function get($filename, $scope)
    {
        // Create pattern similar to app/locale/*/config.xml
        $filePattern = $this->filesystem->getPath(Filesystem::LOCALE) . '/' . '*' . '/' . $filename;
        return glob($filePattern, GLOB_BRACE);
    }
}
