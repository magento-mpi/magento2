<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Setup\Model;

use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * License file reader
 *
 * @package Magento\Setup\Model
 */
class License
{
    /**
     * License File location
     *
     * @var string
     */
    const LICENSE_FILENAME = 'LICENSE.txt';


    /**
     * Directory that contains license file
     *
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface
     */
    private $dir;

    /**
     * Constructor
     *
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->dir = $filesystem->getDirectoryRead(DirectoryList::ROOT);
    }

    /**
     * Returns contents of License file.
     *
     * @return string
     */
    public function getContents()
    {
        if (!$this->dir->isFile(self::LICENSE_FILENAME)) {
            return false;
        }
        return $this->dir->readFile(self::LICENSE_FILENAME);
    }
}
