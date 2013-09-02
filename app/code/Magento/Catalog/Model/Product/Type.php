<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product type model
 */
class Magento_Catalog_Model_Product_Type
{
    /**#@+
     * Available product types
     */
    const TYPE_SIMPLE       = 'simple';
    const TYPE_BUNDLE       = 'bundle';
    const TYPE_CONFIGURABLE = 'configurable';
    const TYPE_GROUPED      = 'grouped';
    const TYPE_VIRTUAL      = 'virtual';
    /**#@-*/

    /**
     * Default product type
     */
    const DEFAULT_TYPE      = 'simple';

    /**
     * Default product type model
     */
    const DEFAULT_TYPE_MODEL    = 'Magento_Catalog_Model_Product_Type_Simple';

    /**
     * Default price model
     */
    const DEFAULT_PRICE_MODEL   = 'Magento_Catalog_Model_Product_Type_Price';

    /**
     * @var Magento_Core_Model_Config_Modules
     */
    protected $_configModules;

    /**
     * Product types
     *
     * @var array|string
     */
    protected $_types;

    /**
     * Composite product type Ids
     *
     * @var array
     */
    protected $_compositeTypes;

    /**
     * Price models
     *
     * @var array
     */
    protected $_priceModels;

    /**
     * Product types by type indexing priority
     *
     * @var array
     */
    protected $_typesPriority;

    /**
     * @param Magento_Core_Model_Config_Modules $configModules
     */
    public function __construct(Magento_Core_Model_Config_Modules $configModules)
    {
        $this->_configModules = $configModules;
    }

    /**
     * Factory to product singleton product type instances
     *
     * @param   Magento_Catalog_Model_Product $product
     * @return  Magento_Catalog_Model_Product_Type_Abstract
     */
    public function factory($product)
    {
        $types = $this->getTypes();
        $typeId = $product->getTypeId();

        if (!empty($types[$typeId]['model'])) {
            $typeModelName = $types[$typeId]['model'];
        } else {
            $typeModelName = self::DEFAULT_TYPE_MODEL;
            $typeId = self::DEFAULT_TYPE;
        }

        /** @var $typeModel Magento_Catalog_Model_Product_Type_Abstract */
        $typeModel = Mage::getSingleton($typeModelName);
        $typeModel->setConfig($types[$typeId]);
        return $typeModel;
    }

    /**
     * Product type price model factory
     *
     * @param   string $productType
     * @return  Magento_Catalog_Model_Product_Type_Price
     */
    public function priceFactory($productType)
    {
        if (isset($this->_priceModels[$productType])) {
            return $this->_priceModels[$productType];
        }

        $types = $this->getTypes();

        if (!empty($types[$productType]['price_model'])) {
            $priceModelName = $types[$productType]['price_model'];
        } else {
            $priceModelName = self::DEFAULT_PRICE_MODEL;
        }

        $this->_priceModels[$productType] = Mage::getModel($priceModelName);
        return $this->_priceModels[$productType];
    }

    /**
     * Get product type labels array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $options = array();
        foreach ($this->getTypes() as $typeId => $type) {
            $options[$typeId] = __($type['label']);
        }
        return $options;
    }

    /**
     * Get product type labels array with empty value
     *
     * @return array
     */
    public function getAllOption()
    {
        $options = $this->getOptionArray();
        array_unshift($options, array('value' => '', 'label' => ''));
        return $options;
    }

    /**
     * Get product type labels array with empty value for option element
     *
     * @return array
     */
    public function getAllOptions()
    {
        $res = array();
        $res[] = array('value' => '', 'label' => '');
        foreach ($this->getOptionArray() as $index => $value) {
            $res[] = array(
               'value' => $index,
               'label' => $value
            );
        }
        return $res;
    }

    /**
     * Get product type labels array for option element
     *
     * @return array
     */
    public function getOptions()
    {
        $res = array();
        foreach ($this->getOptionArray() as $index => $value) {
            $res[] = array(
               'value' => $index,
               'label' => $value
            );
        }
        return $res;
    }

    /**
     * Get product type label
     *
     * @param string $optionId
     * @return null|string
     */
    public function getOptionText($optionId)
    {
        $options = $this->getOptionArray();
        return isset($options[$optionId]) ? $options[$optionId] : null;
    }

    /**
     * Get product types
     *
     * @return array
     */
    public function getTypes()
    {
        if (is_null($this->_types)) {
            $productTypes = $this->_configModules->getNode('global/catalog/product/type')->asArray();
            foreach ($productTypes as $productKey => $productConfig) {
                $productTypes[$productKey]['label'] = __($productConfig['label']);
            }
            $this->_types = $productTypes;
        }
        return $this->_types;
    }

    /**
     * Return composite product type Ids
     *
     * @return array
     */
    public function getCompositeTypes()
    {
        if (is_null($this->_compositeTypes)) {
            $this->_compositeTypes = array();
            $types = $this->getTypes();
            foreach ($types as $typeId => $typeInfo) {
                if (array_key_exists('composite', $typeInfo) && $typeInfo['composite']) {
                    $this->_compositeTypes[] = $typeId;
                }
            }
        }
        return $this->_compositeTypes;
    }

    /**
     * Return product types by type indexing priority
     *
     * @return array
     */
    public function getTypesByPriority()
    {
        if (is_null($this->_typesPriority)) {
            $this->_typesPriority = array();
            $simplePriority = array();
            $compositePriority = array();

            $types = $this->getTypes();
            foreach ($types as $typeId => $typeInfo) {
                $priority = isset($typeInfo['index_priority']) ? abs(intval($typeInfo['index_priority'])) : 0;
                if (!empty($typeInfo['composite'])) {
                    $compositePriority[$typeId] = $priority;
                } else {
                    $simplePriority[$typeId] = $priority;
                }
            }

            asort($simplePriority, SORT_NUMERIC);
            asort($compositePriority, SORT_NUMERIC);

            foreach (array_keys($simplePriority) as $typeId) {
                $this->_typesPriority[$typeId] = $types[$typeId];
            }
            foreach (array_keys($compositePriority) as $typeId) {
                $this->_typesPriority[$typeId] = $types[$typeId];
            }
        }
        return $this->_typesPriority;
    }
}
