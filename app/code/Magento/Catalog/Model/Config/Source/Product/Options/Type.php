<?php
/**
 * Product option types mode source
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Catalog_Model_Config_Source_Product_Options_Type implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * @var Magento_Catalog_Model_ProductOptions_ConfigInterface
     */
    protected $_productOptionConfig;

    /**
     * @param Magento_Catalog_Model_ProductOptions_ConfigInterface $productOptionConfig
     */
    public function __construct(Magento_Catalog_Model_ProductOptions_ConfigInterface $productOptionConfig)
    {
        $this->_productOptionConfig = $productOptionConfig;
    }


    public function toOptionArray()
    {
        $groups = array(
            array('value' => '', 'label' => __('-- Please select --'))
        );

        foreach ($this->_productOptionConfig->getAll() as $option) {
            $types = array();
            foreach ($option['types'] as $type) {
                if ($type['disabled']) {
                    continue;
                }
                $types[] = array(
                    'label' => __($type['label']),
                    'value' => $type['name']
                );
            }
            if (count($types)) {
                $groups[] = array(
                    'label' => __($option['label']),
                    'value' => $types
                );
            }
        }

        return $groups;
    }
}
