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

use Mtf\Fixture;
use Mtf\Handler\Direct;
use Mtf\Factory\Factory;

/**
 * Class CreateCategory
 *
 * @package Magento\Catalog\Test\Handler\Direct
 */
class CreateCategory extends Direct
{
    /**
     * Create Category
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

        /** @var $product \Mage_Catalog_Model_Category */
        $category = $objectManager->create('Mage_Catalog_Model_Category');

        $dataSet = $fixture->getData();
        $data = $this->_convertData($dataSet);
        $category->isObjectNew(true);
        $category->setData($data);
        $category->save();

        return $category->getId();
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
        if (!$fields) {
            return array();
        }

        foreach ($fields as $field => $attributes) {
            if ($attributes['value'] == 'Yes' || $attributes['value'] == 'No') {
                $value = $attributes['value'] == 'Yes' ? 1 : 0;
            } else {
                $value = $attributes['value'];
            }
            $newData[$field] = $value;
        }

        return $newData;
    }
}
