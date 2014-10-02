<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Ui\DataProvider;

use Magento\Customer\Service\V1\AddressMetadataService;
use Magento\Framework\View\Element\UiComponent\Context;
use Magento\Ui\DataProvider\DataProviderCollectionInterface;
use \Magento\Customer\Model\Resource\Address\Collection as AddressCollection;

/**
 * Class CustomerAddressCollection
 */
class CustomerAddressCollection implements DataProviderCollectionInterface
{
    /**
     * Customer meta
     *
     * @var AddressMetadataService
     */
    protected $customerAddressMeta;

    /**
     * Customer address collection
     *
     * @var AddressCollection
     */
    protected $collection;

    /**
     * Collection filter
     *
     * @var array
     */
    protected $filter = [];

    /**
     * Data provider arguments
     *
     * @var array
     */
    protected $arguments;

    /**
     * Render context
     *
     * @var Context
     */
    protected $renderContext;

    /**
     * Constructor
     *
     * @param AddressMetadataService $customerAddressMeta
     * @param AddressCollection $collection
     * @param Context $renderContext
     * @param array $arguments
     */
    public function __construct(
        AddressMetadataService $customerAddressMeta,
        AddressCollection $collection,
        Context $renderContext,
        array $arguments = []
    ) {
        $this->customerMeta = $customerAddressMeta;
        $this->collection = $collection;
        $this->arguments = $arguments;
        $this->renderContext = $renderContext;
    }

    /**
     * Get meta data
     *
     * @return array
     */
    public function getMeta()
    {
        $metaResult = [];
        /** @var \Magento\Customer\Service\V1\Data\Eav\AttributeMetadata $dataObject */
        foreach ($this->customerMeta->getAttributes('adminhtml_customer_address') as $name => $dataObject) {
            $metaResult[$name] = $dataObject->__toArray();
        }

        return $metaResult;
    }

    /**
     * Get data
     *
     * @return \Magento\Framework\Object[]
     */
    public function getData()
    {
        $this->setFilters();
        $items = $this->collection->addAttributeToSelect('*')->getItems();
        return $items;
    }

    /**
     * Get argument values
     *
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    protected function getArguments($key, $default = null)
    {
        return isset($this->arguments[$key]) ? $this->arguments[$key] : $default;
    }

    /**
     * Set filters
     *
     * @return void
     */
    protected function setFilters()
    {
        $this->filter = array_merge($this->getArguments('filter'), $this->filter);
        foreach ($this->filter as $filter) {
            $data = null;
            if (isset($filter['data_provider'])) {
                $dataProvider = $this->renderContext->getStorage()->getDataProvider($filter['data_provider']['name']);
                $data = $dataProvider->getData();
                $data = isset($data[$filter['data_provider']['field']])
                    ? $data[$filter['data_provider']['field']]
                    : null;
            }
            if ($data !== null) {
                $this->collection->addFieldToFilter($filter['field'], [$filter['filter_type'] => $data]);
            }
        }
    }

    /**
     * Add a filter to the data
     *
     * @param array $filter
     * @return void
     */
    public function addFilter(array $filter)
    {
        $this->filter[] = $filter;
    }
}
