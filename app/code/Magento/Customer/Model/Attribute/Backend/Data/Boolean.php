<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Model\Attribute\Backend\Data;

/**
 * Boolean customer attribute backend model
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Boolean
    extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * Prepare data before attribute save
     *
     * @param \Magento\Customer\Model\Customer $customer
     * @return $this
     */
    public function beforeSave($customer)
    {
        $attributeName = $this->getAttribute()->getName();
        $inputValue = $customer->getData($attributeName);
        $sanitizedValue = (!empty($inputValue)) ? '1' : '0';
        $customer->setData($attributeName, $sanitizedValue);
        return $this;
    }
}
