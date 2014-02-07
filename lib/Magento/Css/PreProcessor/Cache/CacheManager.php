<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Css\PreProcessor\Cache;

use Magento\Exception;
use Magento\Filesystem;

/**
 * Less cache manager
 */
class CacheManager implements CacheManagerInterface
{
    /**
     * @var array
     */
    protected $importEntities = [];

    /**
     * @var string
     */
    protected $cachedFile;

    /**
     * @var string
     */
    protected $uniqueFileKey;

    /**
     * @var Import\Map\Storage
     */
    protected $storage;

    /**
     * @var Import\ImportEntityFactory
     */
    protected $importEntityFactory;

    /**
     * @param Import\Map\Storage $storage
     * @param Import\ImportEntityFactory $importEntityFactory
     * @param string $filePath
     * @param array $params
     */
    public function __construct(
        Import\Map\Storage $storage,
        Import\ImportEntityFactory $importEntityFactory,
        $filePath,
        $params
    ) {
        $this->storage = $storage;
        $this->importEntityFactory = $importEntityFactory;
        $this->uniqueFileKey = $this->prepareKey($filePath, $params);

        $this->loadImportEntities();
    }

    /**
     * @param string $filePath
     * @param array $params
     * @return string
     */
    protected function prepareKey($filePath, $params)
    {
        if (!empty($params['themeModel'])) {
            $themeModel = $params['themeModel'];
            $params['themeModel'] = $themeModel->getId() ?: md5($themeModel->getThemePath());
        }
        ksort($params);
        return $filePath . '|' . implode('|', $params);
    }

    /**
     * @return $this
     */
    protected function loadImportEntities()
    {
        $importEntities = unserialize($this->storage->load($this->uniqueFileKey));
        $this->cachedFile = isset($importEntities['cached_file']) ? $importEntities['cached_file'] : null;
        $this->importEntities = isset($importEntities['imports']) ? $importEntities['imports'] : [];
        if (!$this->isValid()) {
            $this->clearCache();
        }
        return $this;
    }

    /**
     * @return bool
     */
    protected function isValid()
    {
        if (empty($this->importEntities)) {
            return false;
        }

        /** @var Import\ImportEntity $entity */
        foreach ($this->importEntities as $entity) {
            if (!$entity->isValid()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return $this
     */
    public function clearCache()
    {
        $this->cachedFile = null;
        $this->importEntities = [];
        $this->storage->delete($this->uniqueFileKey);
        return $this;
    }

    /**
     * @return null|string
     */
    public function getCachedFile()
    {
        return $this->cachedFile;
    }

    /**
     * @param string $filePath
     * @param array $params
     * @return $this
     */
    public function addEntityToCache($filePath, $params)
    {
        $fileKey = $this->prepareKey($filePath, $params);
        $this->importEntities[$fileKey] = $this->importEntityFactory->create($filePath, $params);
        return $this;
    }

    /**
     * @param string $generatedFile
     * @return $this
     */
    public function saveCache($generatedFile)
    {
        $this->storage->save($this->uniqueFileKey, $this->prepareSaveData($generatedFile));
        return $this;
    }

    /**
     * @param string $cachedFile
     * @return string
     */
    protected function prepareSaveData($cachedFile)
    {
        return serialize(['cached_file' => $cachedFile, 'imports' => $this->importEntities]);
    }
}
