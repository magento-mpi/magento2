<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Ui\DataProvider;

use Magento\Customer\Service\V1\CustomerMetadataServiceInterface;
use Magento\Ui\DataProvider\DataProviderEntityInterface;
use Magento\Customer\Model\Customer as CustomerObject;

/**
 * Class Customer
 */
class Customer implements DataProviderEntityInterface
{
    /**
     * Customer meta
     *
     * @var CustomerMetadataServiceInterface
     */
    protected $customerMeta;

    /**
     * @var CustomerObject
     */
    protected $customer;

    /**
     * Data provider arguments
     *
     * @var array
     */
    protected $arguments;

    /**
     * Constructor
     *
     * @param CustomerMetadataServiceInterface $customerMeta
     * @param CustomerObject $customer
     * @param array $arguments
     */
    public function __construct(
        CustomerMetadataServiceInterface $customerMeta,
        CustomerObject $customer,
        array $arguments = []
    ) {
        $this->customerMeta = $customerMeta;
        $this->customer = $customer;
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
        foreach ($this->customerMeta->getAttributes('adminhtml_customer') as $name => $dataObject) {
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
        $this->loadByField($this->getArguments(self::CONFIG_KEY));
        return $this->customer->getData();
    }

    /**
     * @param $field
     */
    public function loadByField($field)
    {
        $params = $this->getArguments('params');
        $fieldValue = isset($params[$field]) ? $params[$field] : null;
        $this->customer->load($fieldValue);
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
}
