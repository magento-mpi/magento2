<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Element\UiComponent;

use Magento\Framework\Data\Collection as DataCollection;
use Magento\Ui\DataProvider\DataProviderInterface;

/**
 * Class ConfigurationStorageInterface
 */
interface ConfigStorageInterface
{
    /**
     * Add components configuration
     *
     * @param ConfigInterface $configuration
     * @return void
     */
    public function addComponentsData(ConfigInterface $configuration);

    /**
     * Remove components configuration
     *
     * @param ConfigInterface $configuration
     * @return void
     */
    public function removeComponentsData(ConfigInterface $configuration);

    /**
     * Get components configuration
     *
     * @param string|null $name
     * @return ConfigInterface|null|array
     */
    public function getComponentsData($name = null);

    /**
     * Add data in storage
     *
     * @param string $key
     * @param array $data
     * @return void
     */
    public function addData($key, array $data);

    /**
     * Remove data in storage
     *
     * @param string $key
     * @return void
     */
    public function removeData($key);

    /**
     * Get data from storage
     *
     * @param string|null $key
     * @return array|null
     */
    public function getData($key = null);

    /**
     * Update data in storage
     *
     * @param string $key
     * @param array $data
     * @return void
     */
    public function updateData($key, array $data);

    /**
     * Add meta data
     *
     * @param string $key
     * @param array $data
     * @return mixed
     */
    public function addMeta($key, array $data);

    /**
     * Remove meta data
     *
     * @param string $key
     * @return array
     */
    public function removeMeta($key);

    /**
     * Get meta data
     *
     * @param string|null $key
     * @return array|null
     */
    public function getMeta($key = null);

    /**
     * Update meta data in storage
     *
     * @param string $key
     * @param array $data
     * @return void
     */
    public function updateMeta($key, array $data);

    /**
     * Set data collection
     *
     * @param string $key
     * @param DataCollection $dataCollection
     * @return void
     */
    public function addDataCollection($key, DataCollection $dataCollection);

    /**
     * Get data collection
     *
     * @param string|null $key
     * @return DataCollection
     */
    public function getDataCollection($key = null);

    /**
     * Update data collection in storage
     *
     * @param string $key
     * @param DataCollection $dataCollection
     * @return mixed
     */
    public function updateDataCollection($key, DataCollection $dataCollection);

    /**
     * Add cloud data in storage
     *
     * @param string $key
     * @param array $data
     * @return void
     */
    public function addGlobalData($key, array $data);

    /**
     * Remove cloud data in storage
     *
     * @param string $key
     * @return void
     */
    public function removeGlobalData($key);

    /**
     * Get cloud data from storage
     *
     * @param string|null $key
     * @return array|null
     */
    public function getGlobalData($key = null);

    /**
     * @param string $key
     * @param DataProviderInterface $dataProvider
     * @return void
     */
    public function addDataProvider($key, DataProviderInterface $dataProvider);

    /**
     * @param string $key
     * @return void
     */
    public function removeDataProvider($key);

    /**
     * @param null|string $key
     * @return DataProviderInterface[]|DataProviderInterface|null
     */
    public function getDataProvider($key = null);

    /**
     * @param string $key
     * @param DataProviderInterface $dataProvider
     * @return void
     */
    public function updateDataProvider($key, DataProviderInterface $dataProvider);
}
