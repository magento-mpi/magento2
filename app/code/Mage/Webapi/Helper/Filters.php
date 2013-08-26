<?php
/**
 * Webapi Helper.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Helper_Filters extends Mage_Core_Helper_Abstract
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
     * @param Mage_Core_Model_Config $config
     */
    public function __construct(
        Mage_Core_Helper_Context $context,
        Mage_Core_Model_Config $config
    ) {
        parent::__construct($context);
        $moduleDir = $config->getModuleDir('etc', 'Mage_Webapi');
        $retriever = new JsonSchema\Uri\UriRetriever();
        $this->_schema = $retriever->retrieve('file://' . $moduleDir . '/json/filters-schema.json');
        $refResolver = new JsonSchema\RefResolver($retriever);
        $refResolver->resolve($this->_schema, 'file://' . $moduleDir . '/json');
        $this->_validator = new JsonSchema\Validator();
    }

    /**
     * Validate the filter value against the JSON schema.
     *
     * @param string $filter - The filter value (e.g. {"name":{"$eq":"tablet"}})
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
            $schemaErrors .= '[' . $error['property'] . '] ' . $error['message'] . PHP_EOL;
        }
        return $schemaErrors;
    }
}
