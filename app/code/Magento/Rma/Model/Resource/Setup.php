<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Model\Resource;

/**
 * Rma resource setup model
 */
class Setup extends \Magento\Sales\Model\Resource\Setup
{
    /**
     * Catalog model setup factory
     *
     * @var \Magento\Catalog\Model\Resource\SetupFactory
     */
    protected $_catalogSetupFactory;

    /**
     * Rma refundable list
     * 
     * @var \Magento\Catalog\Model\ProductTypes\ConfigInterface
     */
    protected $productTypeConfig;

    /**
     * @param \Magento\Eav\Model\Entity\Setup\Context $context
     * @param string $resourceName
     * @param \Magento\App\CacheInterface $cache
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\Group\CollectionFactory $attrGroupCollectionFactory
     * @param \Magento\App\ConfigInterface $config
     * @param \Magento\Catalog\Model\Resource\SetupFactory $catalogSetupFactory
     * @param \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig
     * @param string $moduleName
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Eav\Model\Entity\Setup\Context $context,
        $resourceName,
        \Magento\App\CacheInterface $cache,
        \Magento\Eav\Model\Resource\Entity\Attribute\Group\CollectionFactory $attrGroupCollectionFactory,
        \Magento\App\ConfigInterface $config,
        \Magento\Catalog\Model\Resource\SetupFactory $catalogSetupFactory,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig,
        $moduleName = 'Magento_Rma',
        $connectionName = \Magento\Module\Updater\SetupInterface::DEFAULT_SETUP_CONNECTION
    ) {
        $this->_catalogSetupFactory = $catalogSetupFactory;
        $this->productTypeConfig = $productTypeConfig;
        parent::__construct(
            $context,
            $resourceName,
            $cache,
            $attrGroupCollectionFactory,
            $config,
            $moduleName,
            $connectionName
        );
    }

    /**
     * Get refundable product types
     *
     * @return array
     */
    public function getRefundableProducts()
    {
        return array_diff(
            $this->productTypeConfig->filter('refundable'),
            $this->productTypeConfig->filter('is_product_set')
        );
    }

    /**
     * Retrieve default RMA item entities
     *
     * @return array
     */
    public function getDefaultEntities()
    {
        $entities = array(
            'rma_item' => array(
                'entity_model' => 'Magento\Rma\Model\Resource\Item',
                'attribute_model' => 'Magento\Rma\Model\Item\Attribute',
                'table' => 'magento_rma_item_entity',
                'increment_model' => 'Magento\Eav\Model\Entity\Increment\Numeric',
                'additional_attribute_table' => 'magento_rma_item_eav_attribute',
                'entity_attribute_collection' => null,
                'increment_per_store' => 1,
                'attributes' => array(
                    'rma_entity_id' => array(
                        'type' => 'static',
                        'label' => 'RMA Id',
                        'input' => 'text',
                        'required' => true,
                        'visible' => false,
                        'sort_order' => 10,
                        'position' => 10
                    ),
                    'order_item_id' => array(
                        'type' => 'static',
                        'label' => 'Order Item Id',
                        'input' => 'text',
                        'required' => true,
                        'visible' => false,
                        'sort_order' => 20,
                        'position' => 20
                    ),
                    'qty_requested' => array(
                        'type' => 'static',
                        'label' => 'Qty of requested for RMA items',
                        'input' => 'text',
                        'required' => true,
                        'visible' => false,
                        'sort_order' => 30,
                        'position' => 30
                    ),
                    'qty_authorized' => array(
                        'type' => 'static',
                        'label' => 'Qty of authorized items',
                        'input' => 'text',
                        'visible' => false,
                        'sort_order' => 40,
                        'position' => 40
                    ),
                    'qty_approved' => array(
                        'type' => 'static',
                        'label' => 'Qty of requested for RMA items',
                        'input' => 'text',
                        'visible' => false,
                        'sort_order' => 50,
                        'position' => 50
                    ),
                    'status' => array(
                        'type' => 'static',
                        'label' => 'Status',
                        'input' => 'select',
                        'source' => 'Magento\Rma\Model\Item\Attribute\Source\Status',
                        'visible' => false,
                        'sort_order' => 60,
                        'position' => 60,
                        'adminhtml_only' => 1
                    ),
                    'product_name' => array(
                        'type' => 'static',
                        'label' => 'Product Name',
                        'input' => 'text',
                        'sort_order' => 70,
                        'position' => 70,
                        'visible' => false,
                        'adminhtml_only' => 1
                    ),
                    'product_sku' => array(
                        'type' => 'static',
                        'label' => 'Product SKU',
                        'input' => 'text',
                        'sort_order' => 80,
                        'position' => 80,
                        'visible' => false,
                        'adminhtml_only' => 1
                    ),
                    'resolution' => array(
                        'type' => 'int',
                        'label' => 'Resolution',
                        'input' => 'select',
                        'sort_order' => 90,
                        'position' => 90,
                        'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Table',
                        'system' => false,
                        'option' => array('values' => array('Exchange', 'Refund', 'Store Credit')),
                        'validate_rules' => 'a:0:{}'
                    ),
                    'condition' => array(
                        'type' => 'int',
                        'label' => 'Item Condition',
                        'input' => 'select',
                        'sort_order' => 100,
                        'position' => 100,
                        'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Table',
                        'system' => false,
                        'option' => array('values' => array('Unopened', 'Opened', 'Damaged')),
                        'validate_rules' => 'a:0:{}'
                    ),
                    'reason' => array(
                        'type' => 'int',
                        'label' => 'Reason to Return',
                        'input' => 'select',
                        'sort_order' => 110,
                        'position' => 110,
                        'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Table',
                        'system' => false,
                        'option' => array('values' => array('Wrong Color', 'Wrong Size', 'Out of Service')),
                        'validate_rules' => 'a:0:{}'
                    ),
                    'reason_other' => array(
                        'type' => 'varchar',
                        'label' => 'Other',
                        'input' => 'text',
                        'validate_rules' => 'a:2:{s:15:"max_text_length";i:255;s:15:"min_text_length";i:1;}',
                        'sort_order' => 120,
                        'position' => 120
                    )
                )
            )
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
        $rma_item = (int)$this->getEntityTypeId('rma_item');

        $attributeIds = array();
        $select = $this->getConnection()->select()->from(
            array('ea' => $this->getTable('eav_attribute')),
            array('entity_type_id', 'attribute_code', 'attribute_id')
        )->where(
            'ea.entity_type_id = ?',
            $rma_item
        );
        foreach ($this->getConnection()->fetchAll($select) as $row) {
            $attributeIds[$row['entity_type_id']][$row['attribute_code']] = $row['attribute_id'];
        }

        $data = array();
        $entities = $this->getDefaultEntities();
        $attributes = $entities['rma_item']['attributes'];
        foreach ($attributes as $attributeCode => $attribute) {
            $attributeId = $attributeIds[$rma_item][$attributeCode];
            $attribute['system'] = isset($attribute['system']) ? $attribute['system'] : true;
            $attribute['visible'] = isset($attribute['visible']) ? $attribute['visible'] : true;
            if ($attribute['system'] != true || $attribute['visible'] != false) {
                $usedInForms = array('default');
                foreach ($usedInForms as $formCode) {
                    $data[] = array('form_code' => $formCode, 'attribute_id' => $attributeId);
                }
            }
        }

        if ($data) {
            $this->getConnection()->insertMultiple($this->getTable('magento_rma_item_form_attribute'), $data);
        }
    }

    /**
     * Get catalog setup
     *
     * @param array $data
     * @return \Magento\Catalog\Model\Resource\Setup
     */
    public function getCatalogSetup(array $data = array())
    {
        return $this->_catalogSetupFactory->create($data);
    }
}
