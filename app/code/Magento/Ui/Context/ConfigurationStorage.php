<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Context;

use Magento\Framework\Data\Collection as DataCollection;
use Magento\Framework\View\Element\UiComponent\ConfigInterface;
use Magento\Framework\View\Element\UiComponent\ConfigStorageInterface;
use Magento\Ui\DataProvider\DataProviderInterface;

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
     * @var DataCollection[]
     */
    protected $collectionStorage = [];

    /**
     * Global data storage
     *
     * @var array
     */
    protected $globalDataStorage = [];

    /**
     * Data provider storage
     *
     * @var array
     */
    protected $dataProviderStorage = [];

    /**
     * @var array
     */
    protected $components = [];

    /**
     * @var array
     */
    protected $layoutNodes = [];

    /**
     * @param string $name
     * @param array $value
     */
    public function addLayoutNode($name, array $value)
    {
        $this->layoutNodes[$name] = $value;
    }

    /**
     * @return array
     */
    public function getLayoutNodes()
    {
        return $this->layoutNodes;
    }

    /**
     * @param string $name
     * @param array $data
     * @return mixed
     */
    public function addComponent($name, $data)
    {
        $this->components[$name] = $data;
    }

    /**
     * @return array
     */
    public function getComponents()
    {
        return $this->components;
    }

    /**
     * @return array
     */
    public function getMetaKeys()
    {
        return array_keys($this->metaStorage);
    }

    /**
     * Add components configuration
     *
     * @param ConfigInterface $config
     * @return void
     */
    public function addComponentsData(ConfigInterface $config)
    {
        if (!isset($this->componentStorage[$config->getName()])) {
            $this->componentStorage[$config->getName()] = $config;
        }
    }

    /**
     * Remove components configuration
     *
     * @param ConfigInterface $configuration
     * @return void
     */
    public function removeComponentsData(ConfigInterface $configuration)
    {
        unset($this->componentStorage[$configuration->getName()]);
    }

    /**
     * Get components configuration
     *
     * @param string|null $name
     * @return ConfigInterface|null|array
     */
    public function getComponentsData($name = null)
    {
        if ($name === null) {
            return $this->componentStorage;
        }
        return isset($this->componentStorage[$name]) ? $this->componentStorage[$name] : null;
    }

    /**
     * Add data in storage
     *
     * @param string $key
     * @param array $data
     * @return void
     */
    public function addData($key, array $data)
    {
        if (!isset($this->dataStorage[$key])) {
            $this->dataStorage[$key] = $data;
        }
    }

    /**
     * Remove data in storage
     *
     * @param string $key
     * @return void
     */
    public function removeData($key)
    {
        unset($this->dataStorage[$key]);
    }

    /**
     * Get data from storage
     *
     * @param string|null $key
     * @return array|null
     */
    public function getData($key = null)
    {
        if ($key === null) {
            return $this->dataStorage;
        }
        return isset($this->dataStorage[$key]) ? $this->dataStorage[$key] : null;
    }

    /**
     * Update data in storage
     *
     * @param string $key
     * @param array $data
     * @return void
     */
    public function updateData($key, array $data)
    {
        if (isset($this->dataStorage[$key])) {
            $this->dataStorage[$key] = $data;
        }
    }

    /**
     * Add meta data
     *
     * @param string $key
     * @param array $data
     * @return mixed
     */
    public function addMeta($key, array $data)
    {
        if (!isset($this->metaStorage[$key])) {
            $this->metaStorage[$key] = $data;
        }
    }

    /**
     * Remove meta data
     *
     * @param string $key
     * @return array
     */
    public function removeMeta($key)
    {
        unset($this->metaStorage[$key]);
    }

    /**
     * Get meta data
     *
     * @param string|null $key
     * @return array
     */
    public function getMeta($key = null)
    {
        if ($key === null) {
            return $this->metaStorage;
        }
        return isset($this->metaStorage[$key]) ? $this->metaStorage[$key] : null;
    }

    /**
     * Update meta data in storage
     *
     * @param string $key
     * @param array $data
     * @return void
     */
    public function updateMeta($key, array $data)
    {
        if (isset($this->metaStorage[$key])) {
            $this->metaStorage[$key] = $data;
        }
    }

    /**
     * Set data collection
     *
     * @param string $key
     * @param DataCollection $dataCollection
     * @return void
     */
    public function addDataCollection($key, DataCollection $dataCollection)
    {
        if (!isset($this->collectionStorage[$key])) {
            $this->collectionStorage[$key] = $dataCollection;
        }
    }

    /**
     * Get data collection
     *
     * @param string|null $key
     * @return DataCollection|null
     */
    public function getDataCollection($key = null)
    {
        if ($key === null) {
            return $this->collectionStorage;
        }
        return isset($this->collectionStorage[$key]) ? $this->collectionStorage[$key] : null;
    }

    /**
     * Update data collection in storage
     *
     * @param string $key
     * @param DataCollection $dataCollection
     * @return mixed
     */
    public function updateDataCollection($key, DataCollection $dataCollection)
    {
        if (isset($this->collectionStorage[$key])) {
            $this->collectionStorage[$key] = $dataCollection;
        }
    }

    /**
     * Add cloud data in storage
     *
     * @param string $key
     * @param array $data
     * @return void
     */
    public function addGlobalData($key, array $data)
    {
        if (!isset($this->globalDataStorage[$key])) {
            $this->globalDataStorage[$key] = $data;
        }
    }

    /**
     * Remove cloud data in storage
     *
     * @param string $key
     * @return void
     */
    public function removeGlobalData($key)
    {
        unset($this->globalDataStorage[$key]);
    }

    /**
     * Get cloud data from storage
     *
     * @param string|null $key
     * @return array|null
     */
    public function getGlobalData($key = null)
    {
        if ($key === null) {
            return $this->globalDataStorage;
        }
        return isset($this->globalDataStorage[$key]) ? $this->globalDataStorage[$key] : null;
    }

    /**
     * @param string $key
     * @param DataProviderInterface $dataProvider
     * @return void
     */
    public function addDataProvider($key, DataProviderInterface $dataProvider)
    {
        if (!isset($this->dataProviderStorage[$key])) {
            $this->dataProviderStorage[$key] = $dataProvider;
        }
    }

    /**
     * @param string $key
     * @return void
     */
    public function removeDataProvider($key)
    {
        if (isset($this->dataProviderStorage[$key])) {
            unset($this->dataProviderStorage[$key]);
        }
    }

    /**
     * @param null $key
     * @return DataProviderInterface[]|DataProviderInterface|null
     */
    public function getDataProvider($key = null)
    {
        if ($key === null) {
            return $this->dataProviderStorage;
        }
        return isset($this->dataProviderStorage[$key]) ? $this->dataProviderStorage[$key] : null;
    }

    /**
     * @param string $key
     * @param DataProviderInterface $dataProvider
     * @return void
     */
    public function updateDataProvider($key, DataProviderInterface $dataProvider)
    {
        if (isset($this->dataProviderStorage[$key])) {
            $this->dataProviderStorage[$key] = $dataProvider;
        }
    }
}
