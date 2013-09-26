<?php
/**
 * Product option types mode source
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Config\Source\Product\Options;

class Type implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * @var \Magento\Catalog\Model\ProductOptions\ConfigInterface
     */
    protected $_productOptionConfig;

    /**
     * @param \Magento\Catalog\Model\ProductOptions\ConfigInterface $productOptionConfig
     */
    public function __construct(\Magento\Catalog\Model\ProductOptions\ConfigInterface $productOptionConfig)
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
