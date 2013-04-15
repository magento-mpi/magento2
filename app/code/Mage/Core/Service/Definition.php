<?php

class Mage_Core_Service_Definition extends Varien_Object
{
    /**
     * @var Varien_Object
     */
    protected $_definitions = null;

    /**
     * @var array $_requestSchemas
     */
    protected $_requestSchemas = array();

    /**
     * @var array $_responseSchemas
     */
    protected $_responseSchemas = array();

    /**
     * @var array $_dataSchemas
     */
    protected $_dataSchemas = array();

    /** @var Magento_ObjectManager */
    protected $_objectManager;

    public function __construct(
        Magento_ObjectManager $objectManager,
        array $definitions = null)
    {
        $this->_objectManager = $objectManager;

        $this->_prepareDefinitions($definitions);
    }

    /**
     * @param string $serviceClass
     * @param string $serviceMethod [optional]
     * @param Varien_Object $requestSchema [optional]
     * @return Varien_Object $requestSchema
     */
    public function getRequestSchema($serviceClass, $serviceMethod = null, $requestSchema = null)
    {
        // supports dependency injection
        if (null !== $requestSchema) {
            return $requestSchema;
        }

        $hash = $serviceClass . '::' . $serviceMethod;
        if (!isset($this->_requestSchemas[$hash])) {
            if (null !== $serviceMethod) {
                $schema = $this->getNode($serviceClass . '/request_schema/' . $serviceMethod);
            }
            if (!$schema) {
                $schema = $this->getNode($serviceClass . '/request_schema/*');
            }
            $this->_requestSchemas[$hash] = $this->_objectManager->get('Mage_Core_Service_RequestSchema');
            $this->_requestSchemas[$hash]->load($schema);
        }

        return $this->_requestSchemas[$hash];
    }

    /**
     * @param string $serviceClass
     * @param string $serviceMethod [optional]
     * @return Varien_Object $requestSchema
     */
    public function getResponseSchema($serviceClass, $serviceMethod = null)
    {
        $hash = $serviceClass . '::' . $serviceMethod;
        if (!isset($this->_responseSchemas[$hash])) {
            if (null !== $serviceMethod) {
                $schema = $this->getNode($serviceClass . '/response_schema/' . $serviceMethod);
            }
            if (!$schema) {
                $schema = $this->getNode($serviceClass . '/response_schema/*');
            }

            $this->_responseSchemas[$hash] = $this->_objectManager->get('Mage_Core_Service_ResponseSchema');
            $this->_responseSchemas[$hash]->load($schema);
        }

        return $this->_responseSchemas[$hash];
    }

    /**
     * @param string $schemaId
     * @return Mage_Core_Service_DataSchema $dataSchema
     */
    public function getDataSchema($schemaId)
    {
        if (!isset($this->_dataSchemas[$schemaId])) {
            $this->_dataSchemas[$schemaId] = $this->_objectManager->get('Mage_Core_Service_DataSchema');
            $this->_dataSchemas[$schemaId]->load($schemaId);
        }

        return $this->_dataSchemas[$schemaId];
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function getNode($path, $default = null)
    {
        $result = $this->getDefinitions()->getData($path);
        if (null !== $default && null === $result) {
            $result = $default;
        }
        return $result;
    }

    public function getDefinitions()
    {
        return $this->_definitions;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * @todo this is a prototype
     *
     * Definitions may be area-specific with an ability to be extended and replaced for target area
     * or better to say for some unique global context
     *
     * Eg, in default case we work with default definitions while when application runs in "safe" mode
     * we have a specific subset of definitions which extend/overrides the default ones
     *
     * Regarding versioning: we design all "internal" definitions to be 100% matched to WEB API needs.
     * When we bootstrap new WEB API we use all "internal" definitions as a first version of WEB API.
     * All upcoming changes for "internal" usage should be implemented within new files
     * and not directly in original definition files.
     * Following this way we have clear way of controlling of when we should introduce new WEB API version.
     * Also we won't need to "replicate" entirely all "internal" definitions to fix WEB API version.
     *
     */
    protected function _prepareDefinitions(array $definitions = null)
    {
        if (null === $definitions) {
            // @todo fields definitions are excerpted from the corresponded original XSD
            // we may embed data when prepare final array of all service definitions
            // or we may use smart iterator and array access object here to loop all references
            $definitions = array(
                'Mage_Catalog_Service_Product'  => array(
                    'request_schema'  => array(
                        // having all schemas defined on the same level will let us to share schemas between methods
                        '*' => array( // `*` - defines default service-level schema
                            '_ref'             => 'entity/catalog_product',

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

                            'filters'          => array(
                                'default'      => null,
                                'content_type' => 'json'
                            ),

                            'store_id'         => array(
                                'default' => null
                            ),
                            'data_namespace'   => 'catalog_product',
                        )
                    ),
                    'response_schema' => array(
                        '*' => array( // `*` - defines default service-level schema
                            '_ref'             => 'entity/catalog_product',
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
                ),
                'Mage_Catalog_Service_Category' => array(
                    'request_schema'  => array(
                        '*' => array( // `*` - defines default service-level schema
                            '_ref'            => 'entity/catalog_category',

                            // BEGIN: EXCERPTED FROM ORIGINAL DEFINITION
                            'entity_id'       => array(
                                'label'      => 'Entity ID',
                                'type'       => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                                'input_type' => 'label',
                                'size'       => null,
                                'identity'   => true,
                                'nullable'   => false,
                                'primary'    => true,
                            ),
                            // END: EXCERPTED FROM ORIGINAL DEFINITION

                            'store_id'        => array(
                                'default' => null
                            ),

                            'filters'         => array(
                                'default'      => null,
                                'content_type' => 'json',
                                'schema'       => 'service/filters.schema'
                            ),

                            'data_namespace'  => 'catalog_category',
                            'response_schema' => array()
                        )
                    ),
                    'response_schema' => array(
                        '*'    => array( // `*` - defines default service-level schema
                            '_ref'      => 'entity/catalog_category',

                            'entity_id' => array(),
                            'name'      => array()
                        ),
                        'item' => array( // defines method-specific schema
                            '_ref'      => 'entity/catalog_category',

                            'entity_id' => array(),
                            'name'      => array(),
                            'is_active' => array(),
                            'parent_id' => array(),
                            'path'      => array(),
                            'url_key'   => array(),
                            'url_path'  => array()
                        )
                    )
                )
            );
        }

        $this->_definitions = new Varien_Object($definitions);
    }
}
