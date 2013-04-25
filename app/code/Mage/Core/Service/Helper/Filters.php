<?php
/**
 * Service Helper.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Service_Helper_Filters extends Mage_Core_Service_Helper_Abstract
{
    /**
     * @var \stdClass
     */
    protected $_schema;

    /**
     * @var JsonSchema\Validator
     */
    protected $_validator;

    /**
     * Deserialize, retrieve, and resolve the JSON schema. Initialize the validator.
     *
     * @param Mage_Core_Helper_Context $context
     */
    public function __construct(Mage_Core_Helper_Context $context)
    {
        parent::__construct($context);
        $moduleDir = Mage::getModuleDir('etc', 'Mage_Core');
        $retriever = new JsonSchema\Uri\UriRetriever();
        $this->_schema = $retriever->retrieve('file://' . $moduleDir . DS . 'filters-schema.json');
        $refResolver = new JsonSchema\RefResolver($retriever);
        $refResolver->resolve($this->_schema, 'file://' . $moduleDir);
        $this->_validator = new JsonSchema\Validator();
    }

    /**
     * Validate the filter value against the JSON schema.
     *
     * @param string $filter - The filter value (e.g. {"name":{"$eq":"iphone"}})
     * @return bool - True if the filter value validates against the schema
     * @throws Mage_Core_Exception
     * @throws JsonSchema\Exception\JsonDecodingException
     */
    public function validate($filter)
    {
        $json = json_decode($filter);
        if ($json === null) {
            throw new JsonSchema\Exception\JsonDecodingException(json_last_error());
        }

        $this->_validator->reset();
        $this->_validator->check($json, $this->_schema);
        if (!$this->_validator->isValid()) {
            throw new Mage_Core_Exception($this->_getSchemaError($this->_validator->getErrors()));
        }

        return true;
    }

    /**
     * Collect all schema validation errors and return them as a concatenated string.
     *
     * @param array $errors
     * @return string
     */
    private function _getSchemaError($errors)
    {
        $schemaErrors = '';
        foreach ($errors as $error) {
            $schemaErrors .=
                $this->__(sprintf("[%s] %s", $error['property'], $error['message'])) . PHP_EOL;
        }
        return $schemaErrors;
    }

    public function applyPaginationToCollection($collection, $request)
    {
        $limit = $request->getLimit();
        if ($limit) {
            $collection->setPageSize($limit);
        }

        $offset = $request->getOffset();
        if ($offset) {
            $collection->setCurPage($offset);
        }
    }

    public function applyFiltersToCollection($collection, $request)
    {
        $filters = $request->getFilters();
        if ($filters) {
            foreach ($filters as $key => $condition) {
                switch ($key) {
                    case '$and':
                        $this->applyAndConditionToCollection($collection, $condition);
                        break;
                    case '$or':
                        $this->applyOrConditionToCollection($collection, $condition);
                        break;
                    case '$func':
                        $this->applyFunctionalConditionToCollection($collection, $key, $condition);
                        break;
                    default:
                        $this->applyAttributeConditionToCollection($collection, $key, $condition);
                }
            }
        }
    }

    public function applyAndConditionToCollection($collection, $condition)
    {
        foreach ($condition as $attribute => $_condition) {
            $collection->addAttributeToFilter($attribute, $_condition);
        }
    }

    public function applyOrConditionToCollection($collection, $condition)
    {
        //
    }

    public function applyAttributeConditionToCollection($collection, $attribute, $condition)
    {
        $collection->addAttributeToFilter($attribute, $condition);
    }

    public function applyFunctionalConditionToCollection($collection, $method, $arguments)
    {
        $collection->$method($arguments);
    }
}
