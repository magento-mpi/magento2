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
namespace Magento\Catalog\Model\Product;

class Type
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
    const DEFAULT_TYPE_MODEL    = 'Magento\Catalog\Model\Product\Type\Simple';

    /**
     * Default price model
     */
    const DEFAULT_PRICE_MODEL   = 'Magento\Catalog\Model\Product\Type\Price';

    /**
     * Product types
     *
     * @var array|string
     */
    static protected $_types;

    /**
     * Composite product type Ids
     *
     * @var array
     */
    static protected $_compositeTypes;

    /**
     * Price models
     *
     * @var array
     */
    static protected $_priceModels;

    /**
     * Product types by type indexing priority
     *
     * @var array
     */
    static protected $_typesPriority;

    /**
     * Factory to product singleton product type instances
     *
     * @param   \Magento\Catalog\Model\Product $product
     * @return  \Magento\Catalog\Model\Product\Type\AbstractType
     */
    public static function factory($product)
    {
        $types = self::getTypes();
        $typeId = $product->getTypeId();

        if (!empty($types[$typeId]['model'])) {
            $typeModelName = $types[$typeId]['model'];
        } else {
            $typeModelName = self::DEFAULT_TYPE_MODEL;
            $typeId = self::DEFAULT_TYPE;
        }

        /** @var $typeModel \Magento\Catalog\Model\Product\Type\AbstractType */
        $typeModel = \Mage::getSingleton($typeModelName);
        $typeModel->setConfig($types[$typeId]);
        return $typeModel;
    }

    /**
     * Product type price model factory
     *
     * @param   string $productType
     * @return  \Magento\Catalog\Model\Product\Type\Price
     */
    public static function priceFactory($productType)
    {
        if (isset(self::$_priceModels[$productType])) {
            return self::$_priceModels[$productType];
        }

        $types = self::getTypes();

        if (!empty($types[$productType]['price_model'])) {
            $priceModelName = $types[$productType]['price_model'];
        } else {
            $priceModelName = self::DEFAULT_PRICE_MODEL;
        }

        self::$_priceModels[$productType] = \Mage::getModel($priceModelName);
        return self::$_priceModels[$productType];
    }

    /**
     * Get product type labels array
     *
     * @return array
     */
    static public function getOptionArray()
    {
        $options = array();
        foreach (self::getTypes() as $typeId => $type) {
            $options[$typeId] = __($type['label']);
        }

        return $options;
    }

    /**
     * Get product type labels array with empty value
     *
     * @return array
     */
    static public function getAllOption()
    {
        $options = self::getOptionArray();
        array_unshift($options, array('value' => '', 'label' => ''));
        return $options;
    }

    /**
     * Get product type labels array with empty value for option element
     *
     * @return array
     */
    static public function getAllOptions()
    {
        $res = array();
        $res[] = array('value' => '', 'label' => '');
        foreach (self::getOptionArray() as $index => $value) {
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
    static public function getOptions()
    {
        $res = array();
        foreach (self::getOptionArray() as $index => $value) {
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
    static public function getOptionText($optionId)
    {
        $options = self::getOptionArray();
        return isset($options[$optionId]) ? $options[$optionId] : null;
    }

    /**
     * Get product types
     *
     * @return array
     */
    static public function getTypes()
    {
        if (is_null(self::$_types)) {
            $config = \Mage::getObjectManager()->get('Magento\Core\Model\Config');
            $productTypes = $config->getNode('global/catalog/product/type')->asArray();
            foreach ($productTypes as $productKey => $productConfig) {
                $productTypes[$productKey]['label'] = __($productConfig['label']);
            }
            self::$_types = $productTypes;
        }

        return self::$_types;
    }

    /**
     * Return composite product type Ids
     *
     * @return array
     */
    static public function getCompositeTypes()
    {
        if (is_null(self::$_compositeTypes)) {
            self::$_compositeTypes = array();
            $types = self::getTypes();
            foreach ($types as $typeId => $typeInfo) {
                if (array_key_exists('composite', $typeInfo) && $typeInfo['composite']) {
                    self::$_compositeTypes[] = $typeId;
                }
            }
        }
        return self::$_compositeTypes;
    }

    /**
     * Return product types by type indexing priority
     *
     * @return array
     */
    public static function getTypesByPriority()
    {
        if (is_null(self::$_typesPriority)) {
            self::$_typesPriority = array();
            $simplePriority = array();
            $compositePriority = array();

            $types = self::getTypes();
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
                self::$_typesPriority[$typeId] = $types[$typeId];
            }
            foreach (array_keys($compositePriority) as $typeId) {
                self::$_typesPriority[$typeId] = $types[$typeId];
            }
        }
        return self::$_typesPriority;
    }
}
