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
 * Catalog product type api
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Model\Product\Type;

class Api extends \Magento\Api\Model\Resource\AbstractResource
{
    /**
     * Retrieve product type list
     *
     * @return array
     */
    public function items()
    {
        $result = array();

        foreach (\Magento\Catalog\Model\Product\Type::getOptionArray() as $type=>$label) {
            $result[] = array(
                'type'  => $type,
                'label' => $label
            );
        }

        return $result;
    }
} // Class \Magento\Catalog\Model\Product\Type\Api End
