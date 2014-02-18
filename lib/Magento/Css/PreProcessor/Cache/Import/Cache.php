<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Css\PreProcessor\Cache\Import;

use Magento\Exception;
use Magento\Filesystem;

/**
 * Less cache manager
 */
class Cache implements \Magento\Css\PreProcessor\Cache\CacheInterface
{
    /**
     * Cache import type
     */
    const IMPORT_CACHE = 'import';

    /**
     * @var array
     */
    protected $importEntities = [];

    /**
     * @var null|\Magento\View\Publisher\FileInterface
     */
    protected $cachedFile;

    /**
     * @var string
     */
    protected $uniqueFileKey;

    /**
     * @var Map\Storage
     */
    protected $storage;

    /**
     * @var ImportEntityFactory
     */
    protected $importEntityFactory;

    /**
     * @var \Magento\View\Publisher\FileFactory
     */
    protected $fileFactory;

    /**
     * @param Map\Storage $storage
     * @param ImportEntityFactory $importEntityFactory
     * @param \Magento\View\Publisher\FileInterface $publisherFile
     * @param \Magento\View\Publisher\FileFactory $fileFactory
     */
    public function __construct(
        Map\Storage $storage,
        ImportEntityFactory $importEntityFactory,
        \Magento\View\Publisher\FileInterface $publisherFile,
        \Magento\View\Publisher\FileFactory $fileFactory
    ) {
        $this->storage = $storage;
        $this->fileFactory = $fileFactory;
        $this->importEntityFactory = $importEntityFactory;
        $this->uniqueFileKey = $this->prepareKey($publisherFile->getFilePath(), $publisherFile->getViewParams());

        $this->loadImportEntities();
    }

    /**
     * @return $this
     */
    public function clear()
    {
        $this->cachedFile = null;
        $this->importEntities = [];
        $this->storage->delete($this->uniqueFileKey);
        return $this;
    }

    /**
     * @return null|\Magento\View\Publisher\FileInterface
     */
    public function get()
    {
        if ($this->cachedFile instanceof \Magento\View\Publisher\FileInterface) {
            return $this->cachedFile;
        }
        return null;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function add($data)
    {
        list($filePath, $params) = $data;
        $fileKey = $this->prepareKey($filePath, $params);
        $this->importEntities[$fileKey] = $this->importEntityFactory->create($filePath, $params);
        return $this;
    }

    /**
     * @param \Magento\View\Publisher\FileInterface $cachedFile
     * @return $this
     */
    public function save($cachedFile)
    {
        $this->storage->save($this->uniqueFileKey, $this->prepareSaveData($cachedFile));
        return $this;
    }

    /**
     * @param string $filePath
     * @param string $params
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
            $this->clear();
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

        /** @var ImportEntity $entity */
        foreach ($this->importEntities as $entity) {
            if (!$entity->isValid()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param \Magento\View\Publisher\FileInterface $cachedFile
     * @return string
     */
    protected function prepareSaveData($cachedFile)
    {
        return serialize(['cached_file' => $cachedFile, 'imports' => $this->importEntities]);
    }
}
