<?php

class Mage_Core_Service_Definition extends Varien_Object
{
    /**  @var Varien_Object */
    protected $_idl = null;

    /**
     * @var array $_requestSchemas
     */
    protected $_requestSchemas = array();

    /**
     * @var array $_responseSchemas
     */
    protected $_responseSchemas = array();

    /** @var Magento_ObjectManager */
    protected $_objectManager;

    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Prepare service arguments
     *
     * @param string $serviceClass
     * @param string $serviceMethod
     * @param mixed $args [optional]
     * @return Mage_Core_Service_Args $args
     */
    public function extractArguments($serviceClass, $serviceMethod, $args = null)
    {
        if ($args instanceof Mage_Core_Service_Args) {
            return $args;
        }

        $requestArgument = $params = array();

        $requestSchema = $this->getRequestSchema($serviceClass, $serviceMethod);

        $requestParams = (array)Mage::app()->getRequest()->getParams($requestSchema->getDataNamespace());

        if (null !== $args) {
            if (is_string($args) || is_numeric($args)) {
                $args = array('id' => $args);
            }
            // TODO: how about an object?
            $params = array_merge($requestParams, $args);
        }

        // @todo how to declare and extract global variables such as `store_id`?

        if ($params) {
            $requestArgument = $this->filter($params, $requestSchema);
        }

        $args = $this->_objectManager->get('Mage_Core_Service_Args');
        $args->setData($requestArgument);

        return $args;
    }

    public function getRequestSchema($serviceClass, $serviceMethod = null)
    {
        $hash = $serviceClass . '::' . $serviceMethod;
        if (!isset($this->_requestSchemas[$hash])) {
            if (null !== $serviceMethod) {
                $schema = $this->getElement($serviceClass . '/request_schema/' . $serviceMethod);
            }
            if (!$schema) {
                $schema = $this->getElement($serviceClass . '/request_schema/*');
            }
            $this->_requestSchemas[$hash] = new Varien_Object($schema);
        }

        return $this->_requestSchemas[$hash];
    }

    public function prepareResponse($serviceClass, $serviceMethod, & $data, $responseSchema = null)
    {
        if (null === $responseSchema) {
            $responseSchema = $this->getResponseSchema($serviceClass, $serviceMethod);
        }

        $result = array();
        foreach ($responseSchema->getFields() as $key => $element) {
            $result[$key] = $this->_fetchData($data, $key, $element, $responseSchema);
        }

        return true;
    }

    public function getResponseSchema($serviceClass, $serviceMethod = null)
    {
        $hash = $serviceClass . '::' . $serviceMethod;
        if (!isset($this->_responseSchemas[$hash])) {
            if (null !== $serviceMethod) {
                $schema = $this->getElement($serviceClass . '/response_schema/' . $serviceMethod);
            }
            if (!$schema) {
                $schema = $this->getElement($serviceClass . '/response_schema/*');
            }

            $this->_responseSchemas[$hash] = new Varien_Object($schema);
        }

        return $this->_responseSchemas[$hash];
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    protected function _fetchData(& $data, $key, $element, $schema)
    {
        if (!empty($element['_elements'])) {
            $result = array();
            foreach ($element['_elements'] as $_key => $_element) {
                $result[$key] = $this->_fetchData($data, $_key, $_element, $schema);
            }
            return $result;
        }

        if (isset($elements['get_callback'])) {
            $result = call_user_func($elements['get_callback'], array(
                'data'   => $data,
                'node'   => $element,
                'schema' => $schema
            ));
        } else {
            if ($data instanceof Varien_Object) {
                $result = $data->getDataUsingMethod($key);
            } else {
                $result = $data[$key];
            }
        }

        return $result;
    }

    public function filter(array $params, Varien_Object $schema)
    {
        foreach ($params as $field => $value) {
            if (!$schema->getData('fields/' . $field)) {
                unset($params[$field]);
            }
        }
        return $params;
    }

    public function getElement($path, $default = null)
    {
        $result = $this->getIDL()->getData($path);
        if (null !== $default && null === $result) {
            $result = $default;
        }
        return $result;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * @todo this is a prototype
     * @return array
     */
    public function getIDL()
    {
        if (null === $this->_idl) {
            // @todo fields definitions are excerpted from the corresponded original XSD
            // we may embed data when prepare final array of all service definitions
            // or we may use smart iterator and array access object here to loop all references
            $scheme = array(
                'Mage_Catalog_Service_Product'  => array(
                    'request_schema'  => array(
                        '*' => array(
                            'fields'         => array(
                                '_ref'             => 'entity/catalogCategory',

                                // BEGIN: EXCERPTED FROM ORIGINAL DEFINITION
                                'product_id'       => array(
                                    'label'      => 'Entity ID',
                                    'type'       => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                                    'input_type' => 'label',
                                    'size'       => null,
                                    'identity'   => true,
                                    'nullable'   => false,
                                    'primary'    => true,
                                ),
                                'attribute_set_id' => array(
                                    'default' => null
                                ),
                                'type_id'          => array(
                                    'default' => null
                                ),
                                // END: EXCERPTED FROM ORIGINAL DEFINITION

                                'store_id'         => array(
                                    'default' => null
                                )
                            ),
                            'data_namespace' => 'catalog_product',
                        )
                    ),
                    'response_schema' => array(
                        '*' => array(
                            'fields' => array(
                                '_ref'             => 'entity/catalogProduct',
                                'name'             => array(
                                    'required' => true
                                ),
                                'prices'           => array(
                                    '_elements' => array(
                                        'price'       => array(),
                                        'tier_prices' => array(
                                            'get_callback' => array(
                                                'Mage_Catalog_Model_Product_Price',
                                                'getTierPrices'
                                            ),
                                            'set_callback' => array(
                                                'Mage_Catalog_Model_Product_Price',
                                                'setTierPrices'
                                            )
                                        )
                                    )
                                ),
                                'media_gallery'    => array(
                                    'get_callback' => array(
                                        'Mage_Catalog_Model_Product_Gallery',
                                        'getData'
                                    ),
                                    'set_callback' => array(
                                        'Mage_Catalog_Model_Product_Gallery',
                                        'setData'
                                    )
                                ),
                                'related_entities' => array(
                                    '_elements' => array(
                                        'crosssells' => array(
                                            'get_callback' => array(
                                                'Mage_Catalog_Model_Product',
                                                'getCrossSellProductCollection'
                                            ),
                                            'set_callback' => array(
                                                'Mage_Catalog_Model_Product',
                                                'setCrossSellProducts'
                                            )
                                        ),
                                        'upsells'    => array(
                                            'get_callback' => array(
                                                'Mage_Catalog_Model_Product',
                                                'getUpSellProductCollection'
                                            ),
                                            'set_callback' => array(
                                                'Mage_Catalog_Model_Product',
                                                'setUpSellProducts'
                                            )
                                        )
                                    )
                                )
                            )
                        )
                    )
                ),
                'Mage_Catalog_Service_Category' => array(
                    'request_schema'  => array(
                        '*' => array(
                            'fields'         => array(
                                '_ref'      => 'entity/catalogCategory',

                                // BEGIN: EXCERPTED FROM ORIGINAL DEFINITION
                                'entity_id' => array(
                                    'label'      => 'Entity ID',
                                    'type'       => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                                    'input_type' => 'label',
                                    'size'       => null,
                                    'identity'   => true,
                                    'nullable'   => false,
                                    'primary'    => true,
                                ),
                                // END: EXCERPTED FROM ORIGINAL DEFINITION

                                'store_id'  => array(
                                    'default' => null
                                )
                            ),
                            'data_namespace' => 'catalog_category'
                        )
                    ),
                    'response_schema' => array(
                        '*'    => array(
                            'fields' => array(
                                '_ref'      => 'entity/catalogCategory',
                                'entity_id' => array(),
                                'name'      => array()
                            )
                        ),
                        'item' => array(
                            'fields' => array(
                                '_ref' => 'entity/catalogCategory',
                                'entity_id' => array(),
                                'name'      => array()
                            )
                        )
                    )
                )
            );
            $this->_idl = new Varien_Object($scheme);
        }

        return $this->_idl;
    }
}
