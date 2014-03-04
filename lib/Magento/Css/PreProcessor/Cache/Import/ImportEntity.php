<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Css\PreProcessor\Cache\Import;

use Magento\Less\PreProcessor\File\Less;

/**
 * Import entity
 */
class ImportEntity implements ImportEntityInterface
{
    /**
     * @var string
     */
    protected $originalFile;

    /**
     * @var int
     */
    protected $originalMtime;

    /**
     * @param Less $lessFile
     */
    public function __construct(Less $lessFile)
    {
        $this->originalFile = $lessFile->getDirectoryRead()->getRelativePath($lessFile->getSourcePath());
        $this->originalMtime = $lessFile->getDirectoryRead()->stat($this->originalFile)['mtime'];
    }

    /**
     * {@inheritdoc}
     */
    public function getOriginalFile()
    {
        return $this->originalFile;
    }

    /**
     * {@inheritdoc}
     */
    public function getOriginalMtime()
    {
        return $this->originalMtime;
    }
}
