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

    /** @var Magento_ObjectManager */
    protected $_objectManager;

    public function __construct(Magento_ObjectManager $objectManager, array $definitions = null)
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
                $schema = $this->getElement($serviceClass . '/request_schema/' . $serviceMethod);
            }
            if (!$schema) {
                $schema = $this->getElement($serviceClass . '/request_schema/*');
            }
            $this->_requestSchemas[$hash] = new Varien_Object($schema);
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

    public function getElement($path, $default = null)
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
                            ),
                            'data_namespace' => 'catalog_product',
                        )
                    ),
                    'response_schema' => array(
                        '*' => array( // `*` - defines default service-level schema
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
                ),
                'Mage_Catalog_Service_Category' => array(
                    'request_schema'  => array(
                        '*' => array( // `*` - defines default service-level schema
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
                            ),

                            'data_namespace'  => 'catalog_category',
                            'response_schema' => array()
                        )
                    ),
                    'response_schema' => array(
                        '*'    => array( // `*` - defines default service-level schema
                            '_ref'      => 'entity/catalogCategory',

                            'entity_id' => array(),
                            'name'      => array()
                        ),
                        'item' => array( // defines method-specific schema
                            '_ref'      => 'entity/catalogCategory',

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
