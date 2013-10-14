<?php
/**
 * {license_notice}
 *
 * @spi
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Handler\Direct;

use Magento\Catalog\Test\Fixture\Product;
use Mtf\Fixture;
use Mtf\Handler\Direct;
use Mtf\Factory\Factory;

/**
 * Class CreateProduct
 *
 * @package Magento\Catalog\Test\Handler\Direct
 */
class CreateProduct extends Direct
{
    /**
     * Convert data from UI to Direct values according to attributes map
     *
     * @var array
     */
    protected $_mappingData = array(
        'visibility' => array(
            Product::VISIBILITY_NOT_VISIBLE => \Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE,
            Product::VISIBILITY_IN_CATALOG  => \Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG,
            Product::VISIBILITY_IN_SEARCH   => \Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_SEARCH,
            Product::VISIBILITY_BOTH        => \Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
        )
    );

    /**
     * Additional data required for executing
     *
     * @var array
     */
    protected $requiredData = array(
        'attribute_set_id' => 4,
        'type_id'          => \Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
        'website_ids'      => array(1),
    );

    /**
     * Create product
     *
     * @param Fixture $fixture [optional]
     * @return int
     */
    public function execute(Fixture $fixture = null)
    {
        $objectManager = \Mage::getObjectManager();
        if ($objectManager == null) {
            $objectManager = new \Mage_Core_Model_ObjectManager(new \Mage_Core_Model_Config_Primary(BP, $_SERVER));
        }

        /** @var $product \Mage_Catalog_Model_Product */
        $product = $objectManager->create('Mage_Catalog_Model_Product');

        $dataSet = $fixture->getData();
        $data = $this->_convertData($dataSet);
        $product->isObjectNew(true);
        $product->setData($data);
        $product->save();

        return $product->getId();
    }

    /**
     * Convert and add additional data required for processing direct handler
     *
     * @param array $data
     * @return array
     */
    protected function _convertData(array $data)
    {
        $newData = array();
        $fields = isset($data['fields']) ? $data['fields'] : array();
        if ($fields) {
            foreach ($fields as $field => $attributes) {
                $value = $attributes['value'];

                if (array_key_exists($field, $this->_mappingData)
                    && array_key_exists($value, $this->_mappingData[$field])
                ) {
                    $newData[$field] = $this->_mappingData[$field][$value];
                } else {
                    $newData[$field] = $value;
                }
            }

            //Add minimal stock data
            $newData['stock_data']['manage_stock'] = isset($newData['qty']) ? 0 : 1;
            $newData['stock_data']['qty'] = isset($newData['qty']) ? $newData['qty'] : null;
            $newData['stock_data']['is_in_stock'] = isset($newData['in_stock']) ? 0 : 1;

            return array_replace_recursive($this->requiredData, $newData);
        } else {
            return array();
        }
    }
}
