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
 * Catalog category landing page attribute source
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Model\Category\Attribute\Source;

class Mode extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = array(
                array(
                    'value' => \Magento\Catalog\Model\Category::DM_PRODUCT,
                    'label' => __('Products only'),
                ),
                array(
                    'value' => \Magento\Catalog\Model\Category::DM_PAGE,
                    'label' => __('Static block only'),
                ),
                array(
                    'value' => \Magento\Catalog\Model\Category::DM_MIXED,
                    'label' => __('Static block and products'),
                )
            );
        }
        return $this->_options;
    }
}
