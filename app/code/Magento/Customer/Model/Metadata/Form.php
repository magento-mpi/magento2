<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Model\Metadata;

class Form
{
    /**
     * @var \Magento\Customer\Service\V1\CustomerMetadataServiceInterface
     */
    protected $_eavMetadataService;

    /**
     * @var ElementFactory
     */
    protected $_elementFactory;

    /**
     * @var string
     */
    protected $_entityType;

    /**
     * @var string
     */
    protected $_formCode;

    /**
     * @var bool
     */
    protected $_ignoreInvisible = true;

    /**
     * @var array
     */
    protected $_filterAttributes = [];

    /**
     * @var bool
     */
    protected $_isAjax = false;

    /**
     * Attribute values
     *
     * @var array
     */
    protected $_attributeValues = [];

    /**
     * @var \Magento\App\RequestInterface
     */
    protected $_httpRequest;

    /**
     * @var \Magento\Module\Dir\Reader
     */
    protected $_modulesReader;

    /**
     * @var \Magento\Validator\ConfigFactory
     */
    protected $_validatorConfigFactory;

    /**
     * @var \Magento\Validator
     */
    protected $_validator;

    /**
     * @var \Magento\Customer\Service\V1\Dto\Eav\AttributeMetadata[]
     */
    protected $_attributes;

    /**
     * @param \Magento\Customer\Service\V1\CustomerMetadataServiceInterface $eavMetadataService
     * @param ElementFactory $elementFactory
     * @param \Magento\App\RequestInterface $httpRequest
     * @param \Magento\Module\Dir\Reader $modulesReader
     * @param \Magento\Validator\ConfigFactory $validatorConfigFactory
     * @param string $entityType
     * @param string $formCode
     * @param array $attributeValues
     * @param bool $ignoreInvisible
     * @param array $filterAttributes
     * @param bool $isAjax
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Customer\Service\V1\CustomerMetadataServiceInterface $eavMetadataService,
        ElementFactory $elementFactory,
        \Magento\App\RequestInterface $httpRequest,
        \Magento\Module\Dir\Reader $modulesReader,
        \Magento\Validator\ConfigFactory $validatorConfigFactory,
        $entityType,
        $formCode,
        array $attributeValues = [],
        $ignoreInvisible = true,
        $filterAttributes = [],
        $isAjax = false
    )  {
        $this->_eavMetadataService = $eavMetadataService;
        $this->_elementFactory = $elementFactory;
        $this->_attributeValues = $attributeValues;
        $this->_entityType = $entityType;
        $this->_formCode = $formCode;
        $this->_ignoreInvisible = $ignoreInvisible;
        $this->_filterAttributes = $filterAttributes;
        $this->_isAjax = $isAjax;
        $this->_httpRequest = $httpRequest;
        $this->_modulesReader = $modulesReader;
        $this->_validatorConfigFactory = $validatorConfigFactory;
    }

    /**
     * Retrieve attributes metadata for the form
     *
     * @return \Magento\Customer\Service\V1\Dto\Eav\AttributeMetadata[]
     */
    public function getAttributes()
    {
        if (!isset($this->_attributes)) {
            $this->_attributes = $this->_eavMetadataService
                ->getAttributes($this->_entityType, $this->_formCode);
        }
        return $this->_attributes;
    }

    /**
     * Retrieve user defined attributes
     *
     * @return \Magento\Customer\Service\V1\Dto\Eav\AttributeMetadata[]
     */
    public function getUserAttributes()
    {
        $result = [];
        foreach ($this->getAttributes() as $attribute) {
            if ($attribute->isUserDefined()) {
                $result[$attribute->getAttributeCode()] = $attribute;
            }
        }
        return $result;
    }

    /**
     * Retrieve system required attributes
     *
     * @return \Magento\Customer\Service\V1\Dto\Eav\AttributeMetadata[]
     */
    public function getSystemAttributes()
    {
        $result = [];
        foreach ($this->getAttributes() as $attribute) {
            if (!$attribute->isUserDefined()) {
                $result[$attribute->getAttributeCode()] = $attribute;
            }
        }
        return $result;
    }

    /**
     * Retrieve filtered attributes
     *
     * @return \Magento\Customer\Service\V1\Dto\Eav\AttributeMetadata[]
     */
    public function getAllowedAttributes()
    {
        $attributes = $this->getAttributes();
        foreach ($attributes as $attributeCode => $attribute) {
            if (
                $this->_ignoreInvisible && !$attribute->isVisible()
                || in_array($attribute->getAttributeCode(), $this->_filterAttributes)
            ) {
                unset($attributes[$attributeCode]);
            }
        }
        return $attributes;
    }

    /**
     * Extract data from request and return associative data array
     *
     * @param \Magento\App\RequestInterface $request
     * @param string $scope the request scope
     * @param boolean $scopeOnly search value only in scope or search value in global too
     * @return array
     */
    public function extractData(\Magento\App\RequestInterface $request, $scope = null, $scopeOnly = true)
    {
        $data = array();
        foreach ($this->getAllowedAttributes() as $attribute) {
            $dataModel = $this->_getAttributeDataModel($attribute);
            $dataModel->setRequestScope($scope);
            $dataModel->setRequestScopeOnly($scopeOnly);
            $data[$attribute->getAttributeCode()] = $dataModel->extractValue($request);
        }
        return $data;
    }

    /**
     * Compact data array to form attribute values
     *
     * @param array $data
     * @return array attribute values
     */
    public function compactData(array $data)
    {
        foreach ($this->getAllowedAttributes() as $attribute) {
            $dataModel = $this->_getAttributeDataModel($attribute);
            $dataModel->setExtractedData($data);
            if (!isset($data[$attribute->getAttributeCode()])) {
                $data[$attribute->getAttributeCode()] = false;
            }
            $attributeCode = $attribute->getAttributeCode();
            $this->_attributeValues[$attributeCode] = $dataModel->compactValue($data[$attributeCode]);
        }

        return $this->_attributeValues;
    }

    /**
     * Restore data array from SESSION to attribute values
     *
     * @param array $data
     * @return array
     */
    public function restoreData(array $data)
    {
        foreach ($this->getAllowedAttributes() as $attribute) {
            $dataModel = $this->_getAttributeDataModel($attribute);
            $dataModel->setExtractedData($data);
            if (!isset($data[$attribute->getAttributeCode()])) {
                $data[$attribute->getAttributeCode()] = false;
            }
            $attributeCode = $attribute->getAttributeCode();
            $this->_attributeValues[$attributeCode] = $dataModel->restoreValue($data[$attributeCode]);
        }
        return $this->_attributeValues;
    }

    /**
     * Return attribute data model by attribute
     *
     * @param \Magento\Customer\Service\V1\Dto\Eav\AttributeMetadata $attribute
     * @return \Magento\Eav\Model\Attribute\Data\AbstractData
     */
    protected function _getAttributeDataModel($attribute)
    {
        $dataModel = $this->_elementFactory->create(
            $attribute,
            isset($this->_attributeValues[$attribute->getAttributeCode()])
                ? $this->_attributeValues[$attribute->getAttributeCode()] : null,
            $this->_entityType,
            $this->_isAjax
        );
        return $dataModel;
    }


    /**
     * Prepare request with data and returns it
     *
     * @param array $data
     * @return \Magento\App\RequestInterface
     */
    public function prepareRequest(array $data)
    {
        $request = clone $this->_httpRequest;
        $request->setParamSources();
        $request->clearParams();
        $request->setParams($data);

        return $request;
    }

    /**
     * Get validator
     *
     * @param array $data
     * @return \Magento\Validator
     */
    protected function _getValidator(array $data)
    {
        if ($this->_validator !== null) {
            return $this->_validator;
        }

        $configFiles = $this->_modulesReader->getConfigurationFiles('validation.xml');
        $validatorFactory = $this->_validatorConfigFactory->create(array('configFiles' => $configFiles));
        $builder = $validatorFactory->createValidatorBuilder('customer', 'form');

        $builder->addConfiguration('metadata_data_validator', array(
                'method' => 'setAttributes',
                'arguments' => array($this->getAllowedAttributes())
            ));
        $builder->addConfiguration('metadata_data_validator', array(
                'method' => 'setData',
                'arguments' => array($data)
            ));
        $builder->addConfiguration('metadata_data_validator', array(
                'method' => 'setEntityType',
                'arguments' => array($this->_entityType)
            ));
        $this->_validator = $builder->createValidator();

        return $this->_validator;
    }

    /**
     * Validate data array and return true or array of errors
     *
     * @param array $data
     * @return boolean|array
     */
    public function validateData(array $data)
    {
        $validator = $this->_getValidator($data);
        if (!$validator->isValid(false)) {
            $messages = array();
            foreach ($validator->getMessages() as $errorMessages) {
                $messages = array_merge($messages, (array)$errorMessages);
            }
            return $messages;
        }
        return true;
    }

    /**
     * Return array of formatted allowed attributes values.
     *
     * @param string $format
     * @return array
     */
    public function outputData($format = \Magento\Eav\Model\AttributeDataFactory::OUTPUT_FORMAT_TEXT)
    {
        $result = array();
        foreach ($this->getAllowedAttributes() as $attribute) {
            $dataModel = $this->_getAttributeDataModel($attribute);
            $result[$attribute->getAttributeCode()] = $dataModel->outputValue($format);
        }
        return $result;
    }
}
