<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webapi\Helper;

use Magento\Integration\Controller\Adminhtml\Integration as IntegrationController;
use Magento\Service\Data\AbstractObject;

class Data extends \Magento\App\Helper\AbstractHelper
{
    /** @var \Magento\Registry */
    protected $_registry;

    /**
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\Registry $registry
     */
    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\Registry $registry
    ) {
        $this->_registry = $registry;
        parent::__construct($context);
    }

    /**
     * @return array
     */
    public function getSelectedResources()
    {
        $selectedResourceIds = array();
        $currentIntegration = $this->_registry->registry(IntegrationController::REGISTRY_KEY_CURRENT_INTEGRATION);
        if ($currentIntegration
            && isset($currentIntegration['resource']) && is_array($currentIntegration['resource'])
        ) {
            $selectedResourceIds = $currentIntegration['resource'];
        }
        return $selectedResourceIds;
    }

    /**
     * Translate service interface name into service name.
     * Example:
     * <pre>
     * - \Magento\Customer\Service\CustomerV1Interface         => customer          // $preserveVersion == false
     * - \Magento\Customer\Service\Customer\AddressV1Interface => customerAddressV1 // $preserveVersion == true
     * - \Magento\Catalog\Service\ProductV2Interface           => catalogProductV2  // $preserveVersion == true
     * </pre>
     *
     * @param string $interfaceName
     * @param bool $preserveVersion Should version be preserved during interface name conversion into service name
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getServiceName($interfaceName, $preserveVersion = true)
    {
        $serviceNameParts = $this->getServiceNameParts($interfaceName, $preserveVersion);
        return lcfirst(implode('', $serviceNameParts));
    }

    /**
     * Identify the list of service name parts including sub-services using class name.
     *
     * Examples of input/output pairs: <br/>
     * - 'Magento\Customer\Service\Customer\AddressV1Interface' => array('Customer', 'Address', 'V1') <br/>
     * - 'Vendor\Customer\Service\Customer\AddressV1Interface' => array('VendorCustomer', 'Address', 'V1) <br/>
     * - 'Magento\Catalog\Service\ProductV2Interface' => array('CatalogProduct', 'V2')
     *
     * @param string $className
     * @param bool $preserveVersion Should version be preserved during class name conversion into service name
     * @return string[]
     * @throws \InvalidArgumentException When class is not valid API service.
     */
    public function getServiceNameParts($className, $preserveVersion = false)
    {
        if (preg_match(\Magento\Webapi\Model\Config::SERVICE_CLASS_PATTERN, $className, $matches)) {
            $moduleNamespace = $matches[1];
            $moduleName = $matches[2];
            $moduleNamespace = ($moduleNamespace == 'Magento') ? '' : $moduleNamespace;
            $serviceNameParts = explode('\\', trim($matches[4], '\\'));
            if ($moduleName == $serviceNameParts[0]) {
                /** Avoid duplication of words in service name */
                $moduleName = '';
            }
            $parentServiceName = $moduleNamespace . $moduleName . array_shift($serviceNameParts);
            array_unshift($serviceNameParts, $parentServiceName);
            if ($preserveVersion) {
                $serviceVersion = $matches[3];
                $serviceNameParts[] = $serviceVersion;
            }
            return $serviceNameParts;
        }
        throw new \InvalidArgumentException(sprintf('The service interface name "%s" is invalid.', $className));
    }

    /**
     * Create new object and initialize its public fields with data retrieved from Data Object.
     *
     * This method processes all nested Data Objects recursively.
     *
     * @param AbstractObject $dataObject
     * @return \stdClass
     * @throws \InvalidArgumentException
     */
    public function unpackDataObject(AbstractObject $dataObject)
    {
        return $this->unpackArray($dataObject->__toArray());
    }

    /**
     * Unpack as an array and convert keys to camelCase to match property names in WSDL
     *
     * @param array $dataArray
     * @return \stdClass
     */
    public function unpackArray(array $dataArray)
    {
        $response = new \stdClass();
        foreach ($dataArray as $fieldName => $fieldValue) {
            if (is_array($fieldValue) && !$this->_isSimpleSequentialArray($fieldValue)) {
                $fieldValue = $this->unpackArray($fieldValue);
            }
            $fieldName = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $fieldName))));
            $response->$fieldName = $fieldValue;
        }
        return $response;
    }

    /**
     * Check if the array is a simple(one dimensional and not nested) and a sequential(non-associative) array
     *
     * @param array $data
     * @return bool
     */
    protected function _isSimpleSequentialArray(array $data)
    {
        foreach ($data as $key => $value) {
            if (is_string($key) || is_array($value)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Convert multidimensional object/array into multidimensional array of primitives.
     *
     * @param object|array $input
     * @return array
     * @throws \InvalidArgumentException
     */
    public function _toArray($input)
    {
        if (!is_object($input) && !is_array($input)) {
            throw new \InvalidArgumentException("Input argument must be an array or object");
        }
        $result = array();
        foreach ((array)$input as $key => $value) {
            if (is_object($value) || is_array($value)) {
                $result[$key] = $this->_toArray($value);
            } else {
                $result[$key] = $value;
            }
        }
        return $result;
    }
}
