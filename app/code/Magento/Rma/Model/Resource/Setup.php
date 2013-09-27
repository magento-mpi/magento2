<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Rma resource setup model
 */
class Magento_Rma_Model_Resource_Setup extends Magento_Sales_Model_Resource_Setup
{
    /**
     * @var Magento_Catalog_Model_Resource_SetupFactory
     */
    protected $_catalogSetupFactory;

    /**
     * @param Magento_Catalog_Model_Resource_SetupFactory $catalogSetupFactory
     * @param Magento_Core_Model_Resource_Setup_MigrationFactory $migrationFactory
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Config_Resource $resourcesConfig
     * @param Magento_Core_Model_Config $config
     * @param Magento_Core_Model_ModuleListInterface $moduleList
     * @param Magento_Core_Model_Resource $resource
     * @param Magento_Core_Model_Config_Modules_Reader $modulesReader
     * @param Magento_Core_Model_Resource_Resource $resourceResource
     * @param Magento_Core_Model_Resource_Theme_CollectionFactory $themeResourceFactory
     * @param Magento_Core_Model_Theme_CollectionFactory $themeFactory
     * @param Magento_Core_Model_Resource_Setup_MigrationFactory $migrationFactory
     * @param $resourceName
     * @param Magento_Core_Model_CacheInterface $cache
     * @param Magento_Eav_Model_Resource_Entity_Attribute_Group_CollectionFactory $attrGrCollFactory
     * @param Magento_Core_Model_Resource_Setup_MigrationFactory $migrationFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Catalog_Model_Resource_SetupFactory $catalogSetupFactory
     * @param Magento_Enterprise_Model_Resource_Setup_MigrationFactory $entMigrationFactory
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Magento_Catalog_Model_Resource_SetupFactory $catalogSetupFactory,
        Magento_Core_Model_Resource_Setup_MigrationFactory $migrationFactory,
        Magento_Core_Model_Logger $logger,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Config_Resource $resourcesConfig,
        Magento_Core_Model_Config $config,
        Magento_Core_Model_ModuleListInterface $moduleList,
        Magento_Core_Model_Resource $resource,
        Magento_Core_Model_Config_Modules_Reader $modulesReader,
        Magento_Core_Model_Resource_Resource $resourceResource,
        Magento_Core_Model_Resource_Theme_CollectionFactory $themeResourceFactory,
        Magento_Core_Model_Theme_CollectionFactory $themeFactory,
        Magento_Core_Model_Resource_Setup_MigrationFactory $migrationFactory,
        $resourceName,
        Magento_Core_Model_CacheInterface $cache,
        Magento_Eav_Model_Resource_Entity_Attribute_Group_CollectionFactory $attrGrCollFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Catalog_Model_Resource_SetupFactory $catalogSetupFactory,
        Magento_Enterprise_Model_Resource_Setup_MigrationFactory $entMigrationFactory
    ) {
        $this->_catalogSetupFactory = $catalogSetupFactory;
        $this->_entMigrationFactory = $entMigrationFactory;
        parent::__construct(
            $logger,
            $eventManager,
            $resourcesConfig,
            $config,
            $moduleList,
            $resource,
            $modulesReader,
            $resourceResource,
            $themeResourceFactory,
            $themeFactory,
            $migrationFactory,
            $resourceName,
            $cache,
            $attrGrCollFactory,
            $coreData
        );
    }

    /**
     * Prepare RMA item attribute values to save in additional table
     *
     * @param array $attr
     * @return array
     */
    protected function _prepareValues($attr)
    {
        $data = parent::_prepareValues($attr);
        $data = array_merge($data, array(
            'is_visible'                => $this->_getValue($attr, 'visible', 1),
            'is_system'                 => $this->_getValue($attr, 'system', 1),
            'input_filter'              => $this->_getValue($attr, 'input_filter', null),
            'multiline_count'           => $this->_getValue($attr, 'multiline_count', 0),
            'validate_rules'            => $this->_getValue($attr, 'validate_rules', null),
            'data_model'                => $this->_getValue($attr, 'data', null),
            'sort_order'                => $this->_getValue($attr, 'position', 0)
        ));
        return $data;
    }

    /**
     * Retreive default RMA item entities
     *
     * @return array
     */
    public function getDefaultEntities()
    {
        $entities = array(
            'rma_item'                           => array(
                'entity_model'                   => 'Magento_Rma_Model_Resource_Item',
                'attribute_model'                => 'Magento_Rma_Model_Item_Attribute',
                'table'                          => 'magento_rma_item_entity',
                'increment_model'                => 'Magento_Eav_Model_Entity_Increment_Numeric',
                'additional_attribute_table'     => 'magento_rma_item_eav_attribute',
                'increment_per_store'            => 1,
                'entity_attribute_collection'    => null,
                'increment_per_store'            => 1,
                'attributes'                     => array(
                    'rma_entity_id'          => array(
                        'type'               => 'static',
                        'label'              => 'RMA Id',
                        'input'              => 'text',
                        'required'           => true,
                        'visible'            => false,
                        'sort_order'         => 10,
                        'position'           => 10,
                    ),
                    'order_item_id'          => array(
                        'type'               => 'static',
                        'label'              => 'Order Item Id',
                        'input'              => 'text',
                        'required'           => true,
                        'visible'            => false,
                        'sort_order'         => 20,
                        'position'           => 20,
                    ),
                    'qty_requested'          => array(
                        'type'               => 'static',
                        'label'              => 'Qty of requested for RMA items',
                        'input'              => 'text',
                        'required'           => true,
                        'visible'            => false,
                        'sort_order'         => 30,
                        'position'           => 30,
                    ),
                    'qty_authorized'         => array(
                        'type'               => 'static',
                        'label'              => 'Qty of authorized items',
                        'input'              => 'text',
                        'visible'            => false,
                        'sort_order'         => 40,
                        'position'           => 40,
                    ),
                    'qty_approved'           => array(
                        'type'               => 'static',
                        'label'              => 'Qty of requested for RMA items',
                        'input'              => 'text',
                        'visible'            => false,
                        'sort_order'         => 50,
                        'position'           => 50,
                    ),
                    'status'                 => array(
                        'type'               => 'static',
                        'label'              => 'Status',
                        'input'              => 'select',
                        'source'             => 'Magento_Rma_Model_Item_Attribute_Source_Status',
                        'visible'            => false,
                        'sort_order'         => 60,
                        'position'           => 60,
                        'adminhtml_only'     => 1,
                    ),
                    'product_name'           => array(
                        'type'               => 'static',
                        'label'              => 'Product Name',
                        'input'              => 'text',
                        'sort_order'         => 70,
                        'position'           => 70,
                        'visible'            => false,
                        'adminhtml_only'     => 1,
                    ),
                    'product_sku'            => array(
                        'type'               => 'static',
                        'label'              => 'Product SKU',
                        'input'              => 'text',
                        'sort_order'         => 80,
                        'position'           => 80,
                        'visible'            => false,
                        'adminhtml_only'     => 1,
                    ),
                    'resolution'             => array(
                        'type'               => 'int',
                        'label'              => 'Resolution',
                        'input'              => 'select',
                        'sort_order'         => 90,
                        'position'           => 90,
                        'source'             => 'Magento_Eav_Model_Entity_Attribute_Source_Table',
                        'system'             => false,
                        'option'             => array('values' => array('Exchange', 'Refund', 'Store Credit')),
                        'validate_rules'     => 'a:0:{}',
                    ),
                    'condition'              => array(
                        'type'               => 'int',
                        'label'              => 'Item Condition',
                        'input'              => 'select',
                        'sort_order'         => 100,
                        'position'           => 100,
                        'source'             => 'Magento_Eav_Model_Entity_Attribute_Source_Table',
                        'system'             => false,
                        'option'             => array('values' => array('Unopened', 'Opened', 'Damaged')),
                        'validate_rules'     => 'a:0:{}',
                    ),
                    'reason'                 => array(
                        'type'               => 'int',
                        'label'              => 'Reason to Return',
                        'input'              => 'select',
                        'sort_order'         => 110,
                        'position'           => 110,
                        'source'             => 'Magento_Eav_Model_Entity_Attribute_Source_Table',
                        'system'             => false,
                        'option'             => array('values' => array('Wrong Color', 'Wrong Size', 'Out of Service')),
                        'validate_rules'     => 'a:0:{}',
                    ),
                    'reason_other'           => array(
                        'type'               => 'varchar',
                        'label'              => 'Other',
                        'input'              => 'text',
                        'validate_rules'     => 'a:2:{s:15:"max_text_length";i:255;s:15:"min_text_length";i:1;}',
                        'sort_order'         => 120,
                        'position'           => 120,
                    ),
                )
            ),
        );
        return $entities;
    }

    /**
     * Add RMA Item Attributes to Forms
     *
     * @return void
     */
    public function installForms()
    {
        $rma_item           = (int)$this->getEntityTypeId('rma_item');

        $attributeIds       = array();
        $select = $this->getConnection()->select()
            ->from(
                array('ea' => $this->getTable('eav_attribute')),
                array('entity_type_id', 'attribute_code', 'attribute_id'))
            ->where('ea.entity_type_id = ?', $rma_item);
        foreach ($this->getConnection()->fetchAll($select) as $row) {
            $attributeIds[$row['entity_type_id']][$row['attribute_code']] = $row['attribute_id'];
        }

        $data       = array();
        $entities   = $this->getDefaultEntities();
        $attributes = $entities['rma_item']['attributes'];
        foreach ($attributes as $attributeCode => $attribute) {
            $attributeId = $attributeIds[$rma_item][$attributeCode];
            $attribute['system'] = isset($attribute['system']) ? $attribute['system'] : true;
            $attribute['visible'] = isset($attribute['visible']) ? $attribute['visible'] : true;
            if ($attribute['system'] != true || $attribute['visible'] != false) {
                $usedInForms = array(
                    'default',
                );
                foreach ($usedInForms as $formCode) {
                    $data[] = array(
                        'form_code'     => $formCode,
                        'attribute_id'  => $attributeId
                    );
                }
            }
        }

        if ($data) {
            $this->getConnection()->insertMultiple($this->getTable('magento_rma_item_form_attribute'), $data);
        }
    }

    /**
     * @param array $data
     * @return Magento_Catalog_Model_Resource_Setup
     */
    public function getCatalogSetup(array $data = array())
    {
        return $this->_catalogSetupFactory->create($data);
    }
}
