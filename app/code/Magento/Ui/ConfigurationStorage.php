<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui;

use Magento\Framework\Data\CollectionDataSourceInterface;
use Magento\Framework\View\Element\UiComponent\ConfigInterface;
use Magento\Framework\View\Element\UiComponent\ConfigStorageInterface;

/**
 * Class ConfigurationStorage
 */
class ConfigurationStorage implements ConfigStorageInterface
{
    /**
     * Components configuration storage
     *
     * @var array
     */
    protected $componentStorage = [];

    /**
     * Data storage
     *
     * @var array
     */
    protected $dataStorage = [];

    /**
     * Meta storage
     *
     * @var array
     */
    protected $metaStorage = [];

    /**
     * Data collection storage
     *
     * @var CollectionDataSourceInterface[]
     */
    protected $collectionStorage = [];

    /**
     * Global data storage
     *
     * @var array
     */
    protected $globalDataStorage = [];

    /**
     * @inheritdoc
     */
    public function addComponentsData(ConfigInterface $configuration)
    {
        if (!isset($this->componentStorage[$configuration->getName()])) {
            $this->componentStorage[$configuration->getName()] = $configuration;
        }
    }

    /**
     * @inheritdoc
     */
    public function removeComponentsData(ConfigInterface $configuration)
    {
        unset($this->componentStorage[$configuration->getName()]);
    }

    /**
     * @inheritdoc
     */
    public function getComponentsData($name = null)
    {
        if ($name === null) {
            return $this->componentStorage;
        }
        return isset($this->componentStorage[$name]) ? $this->componentStorage[$name] : null;
    }

    /**
     * @inheritdoc
     */
    public function addData($key, array $data)
    {
        if (!isset($this->dataStorage[$key])) {
            $this->dataStorage[$key] = $data;
        }
    }

    /**
     * @inheritdoc
     */
    public function removeData($key)
    {
        unset($this->dataStorage[$key]);
    }

    /**
     * @inheritdoc
     */
    public function getData($key = null)
    {
        if ($key === null) {
            return $this->dataStorage;
        }
        return isset($this->dataStorage[$key]) ? $this->dataStorage[$key] : null;
    }

    /**
     * @inheritdoc
     */
    public function updateData($key, array $data)
    {
        if (isset($this->dataStorage[$key])) {
            $this->dataStorage[$key] = $data;
        }
    }

    /**
     * @inheritdoc
     */
    public function addMeta($key, array $data)
    {
        if (!isset($this->metaStorage[$key])) {
            $this->metaStorage[$key] = $data;
        }
    }

    /**
     * @inheritdoc
     */
    public function removeMeta($key)
    {
        unset($this->metaStorage[$key]);
    }

    /**
     * @inheritdoc
     */
    public function getMeta($key = null)
    {
        if ($key === null) {
            return $this->metaStorage;
        }
        return isset($this->metaStorage[$key]) ? $this->metaStorage[$key] : null;
    }

    /**
     * @inheritdoc
     */
    public function updateMeta($key, array $data)
    {
        if (isset($this->metaStorage[$key])) {
            $this->metaStorage[$key] = $data;
        }
    }

    /**
     * @inheritdoc
     */
    public function addDataCollection($key, CollectionDataSourceInterface $dataCollection)
    {
        if (!isset($this->collectionStorage[$key])) {
            $this->collectionStorage[$key] = $dataCollection;
        }
    }

    /**
     * @inheritdoc
     */
    public function getDataCollection($key = null)
    {
        if ($key === null) {
            return $this->collectionStorage;
        }
        return isset($this->collectionStorage[$key]) ? $this->collectionStorage[$key] : null;
    }

    /**
     * @inheritdoc
     */
    public function updateDataCollection($key, CollectionDataSourceInterface $dataCollection)
    {
        if (isset($this->collectionStorage[$key])) {
            $this->collectionStorage[$key] = $dataCollection;
        }
    }

    /**
     * @inheritdoc
     */
    public function addGlobalData($key, array $data)
    {
        if (!isset($this->globalDataStorage[$key])) {
            $this->globalDataStorage[$key] = $data;
        }
    }

    /**
     * @inheritdoc
     */
    public function removeGlobalData($key)
    {
        unset($this->globalDataStorage[$key]);
    }

    /**
     * @inheritdoc
     */
    public function getGlobalData($key = null)
    {
        if ($key === null) {
            return $this->globalDataStorage;
        }
        return isset($this->globalDataStorage[$key]) ? $this->globalDataStorage[$key] : null;
    }
}
