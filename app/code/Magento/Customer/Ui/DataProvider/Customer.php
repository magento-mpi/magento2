<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Ui\DataProvider;

use Magento\Customer\Service\V1\CustomerMetadataServiceInterface;
use Magento\Ui\DataProvider\DataProviderInterface;

/**
 * Class Customer
 */
class Customer implements DataProviderInterface
{
    /**
     * Customer meta
     *
     * @var CustomerMetadataServiceInterface
     */
    protected $customerMeta;

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
     * @param array $arguments
     */
    public function __construct(CustomerMetadataServiceInterface $customerMeta, array $arguments = [])
    {
        $this->customerMeta = $customerMeta;
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
        foreach ($this->customerMeta->getAttributes($this->getArguments('form_code')) as $name => $dataObject) {
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
        // TODO: Implement getData() method.
    }

    /**
     * Add a filter to the data
     *
     * @param array $filter
     * @return void
     */
    public function addFilter(array $filter)
    {
        // TODO: Implement addFilter() method.
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