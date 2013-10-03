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
namespace Magento\Rma\Model\Resource;

class Setup extends \Magento\Sales\Model\Resource\Setup
{
    /**
     * @var \Magento\Catalog\Model\Resource\SetupFactory
     */
    protected $_catalogSetupFactory;

    /**
     * @var \Magento\Enterprise\Model\Resource\Setup\MigrationFactory
     */
    protected $_entMigrationFactory;

    /**
     * @param \Magento\Core\Model\Resource\Setup\Context $context
     * @param \Magento\Core\Model\CacheInterface $cache
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\Group\CollectionFactory $attrGrCollFactory
     * @param \Magento\Core\Helper\Data $coreHelper
     * @param \Magento\Core\Model\Config $config
     * @param \Magento\Catalog\Model\Resource\SetupFactory $catalogSetupFactory
     * @param \Magento\Enterprise\Model\Resource\Setup\MigrationFactory $entMigrationFactory
     * @param string $resourceName
     * @param string $moduleName
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Core\Model\Resource\Setup\Context $context,
        \Magento\Core\Model\CacheInterface $cache,
        \Magento\Eav\Model\Resource\Entity\Attribute\Group\CollectionFactory $attrGrCollFactory,
        \Magento\Core\Helper\Data $coreHelper,
        \Magento\Core\Model\Config $config,
        \Magento\Catalog\Model\Resource\SetupFactory $catalogSetupFactory,
        \Magento\Enterprise\Model\Resource\Setup\MigrationFactory $entMigrationFactory,
        $resourceName,
        $moduleName = 'Magento_Rma',
        $connectionName = ''
    ) {
        $this->_catalogSetupFactory = $catalogSetupFactory;
        $this->_entMigrationFactory = $entMigrationFactory;
        parent::__construct(
            $context, $cache, $attrGrCollFactory, $coreHelper, $config, $resourceName, $moduleName, $connectionName
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
                'entity_model'                   => 'Magento\Rma\Model\Resource\Item',
                'attribute_model'                => 'Magento\Rma\Model\Item\Attribute',
                'table'                          => 'magento_rma_item_entity',
                'increment_model'                => 'Magento\Eav\Model\Entity\Increment\Numeric',
                'additional_attribute_table'     => 'magento_rma_item_eav_attribute',
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
                        'source'             => 'Magento\Rma\Model\Item\Attribute\Source\Status',
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
                        'source'             => 'Magento\Eav\Model\Entity\Attribute\Source\Table',
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
                        'source'             => 'Magento\Eav\Model\Entity\Attribute\Source\Table',
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
                        'source'             => 'Magento\Eav\Model\Entity\Attribute\Source\Table',
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
     * @return \Magento\Catalog\Model\Resource\Setup
     */
    public function getCatalogSetup(array $data = array())
    {
        return $this->_catalogSetupFactory->create($data);
    }
}
