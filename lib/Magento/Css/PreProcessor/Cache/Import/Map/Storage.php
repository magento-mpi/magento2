<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Css\PreProcessor\Cache\Import\Map;

use Magento\App\Filesystem;

/**
 * Storage for import cache
 */
class Storage
{
    /**
     * Maps directory for less files
     */
    const MAPS_DIR = "maps/less";

    /**
     * @var \Magento\Filesystem\Directory\WriteInterface
     */
    protected $mapsDirectory;

    /**
     * @param Filesystem $filesystem
     */
    public function __construct(
        Filesystem $filesystem
    ) {
        $this->mapsDirectory = $filesystem->getDirectoryWrite(Filesystem::VAR_DIR);
        if (!$this->mapsDirectory->isDirectory(self::MAPS_DIR)) {
            $this->mapsDirectory->create(self::MAPS_DIR);
        }
    }

    /**
     * @param string $key
     * @return string
     */
    public function load($key)
    {
        $mapFileName = $this->getMapFilePath($key);
        if ($this->mapsDirectory->isFile($mapFileName)) {
            return $this->mapsDirectory->readFile($mapFileName);
        }

        return false;
    }

    /**
     * @param string $key
     * @param string $data
     * @return $this
     */
    public function save($key, $data)
    {
        $mapFileName = $this->getMapFilePath($key);
        $this->mapsDirectory->writeFile($mapFileName, $data);
        return $this;
    }

    /**
     * @param string $key
     * @return $this
     */
    public function delete($key)
    {
        $this->save($key, '');
        return $this;
    }

    /**
     * @return $this
     */
    public function clearImportCache()
    {
        if ($this->mapsDirectory->isDirectory(self::MAPS_DIR)) {
            $this->mapsDirectory->delete(self::MAPS_DIR);
        }
        return $this;
    }

    /**
     * @param string $key
     * @return string
     */
    protected function getMapFilePath($key)
    {
        return self::MAPS_DIR . '/' . md5($key) . '.ser';
    }
}
