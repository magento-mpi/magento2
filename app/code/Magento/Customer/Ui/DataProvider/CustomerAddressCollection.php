<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Ui\DataProvider;

use Magento\Customer\Service\V1\AddressMetadataService;
use Magento\Ui\DataProvider\DataProviderCollectionInterface;
use Magento\Customer\Model\Customer as CustomerObject;

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
     * Data provider arguments
     *
     * @var array
     */
    protected $arguments;

    /**
     * Constructor
     *
     * @param AddressMetadataService $customerAddressMeta
     * @param array $arguments
     */
    public function __construct(
        AddressMetadataService $customerAddressMeta,
        array $arguments = []
    ) {
        $this->customerMeta = $customerAddressMeta;
        $this->arguments = $arguments;
    }

    /**
     * Get meta data
     *
     * @return array
     */
    public function getMeta()
    {
        $metaResult = [];
        foreach ($this->customerMeta->getAttributes('adminhtml_customer_address') as $name => $dataObject) {
            $metaResult[$name] = $dataObject->__toArray();
        }

        return $metaResult;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
//        $this->loadByField($this->getArguments(self::CONFIG_KEY));
        return [];
    }

    /**
     * @param $field
     */
    public function loadByField($field)
    {
//        $params = $this->getArguments('params');
//        $fieldValue = isset($params[$field]) ? $params[$field] : null;
//        $this->customer->load($fieldValue);
    }

        /**
     * Get argument values
     *
     * @param string $key
     * @return mixed
     */
    protected function getArguments($key)
    {
        return isset($this->arguments[$key]) ? $this->arguments[$key] : null;
    }

    /**
     * Add a filter to the data
     *
     * @param array $filter
     * @return void
     */
    public function addFilter(array $filter)
    {

    }
}
