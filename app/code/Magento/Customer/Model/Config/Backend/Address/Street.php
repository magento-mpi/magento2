<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer Address Street Model
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Customer\Model\Config\Backend\Address;

class Street extends \Magento\Core\Model\Config\Value
{
    /**
     * Actions after save
     *
     * @return \Magento\Customer\Model\Config\Backend\Address\Street
     */
    protected function _afterSave()
    {
        $attribute = \Mage::getSingleton('Magento\Eav\Model\Config')->getAttribute('customer_address', 'street');
        $value  = $this->getValue();
        switch ($this->getScope()) {
            case 'websites':
                $website = \Mage::app()->getWebsite($this->getWebsiteCode());
                $attribute->setWebsite($website);
                $attribute->load($attribute->getId());
                if ($attribute->getData('multiline_count') != $value) {
                    $attribute->setData('scope_multiline_count', $value);
                }
                break;

            case 'default':
                $attribute->setData('multiline_count', $value);
                break;
        }
        $attribute->save();
        return $this;
    }

    /**
     * Processing object after delete data
     *
     * @return \Magento\Core\Model\AbstractModel
     */
    protected function _afterDelete()
    {
        $result = parent::_afterDelete();

        if ($this->getScope() == 'websites') {
            $attribute = \Mage::getSingleton('Magento\Eav\Model\Config')->getAttribute('customer_address', 'street');
            $website = \Mage::app()->getWebsite($this->getWebsiteCode());
            $attribute->setWebsite($website);
            $attribute->load($attribute->getId());
            $attribute->setData('scope_multiline_count', null);
            $attribute->save();
        }

        return $result;
    }
}
