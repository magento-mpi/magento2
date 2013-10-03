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
 * Catalog product weight backend attribute model
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Model\Product\Attribute\Backend;

class Weight extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{

    /**
     * Validate
     *
     * @param \Magento\Catalog\Model\Product $object
     * @throws \Magento\Core\Exception
     * @return bool
     */
    public function validate($object)
    {
        $attrCode = $this->getAttribute()->getAttributeCode();
        $value = $object->getData($attrCode);
        if (!empty($value) && !\Zend_Validate::is($value, 'Between', array('min' => 0, 'max' => 99999999.9999))) {
            throw new \Magento\Core\Exception(
                __('Please enter a number 0 or greater in this field.')
            );
        }
        return true;
    }
}
