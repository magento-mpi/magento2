<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Block;

class Agreements extends \Magento\Core\Block\Template
{
    public function getAgreements()
    {
        if (!$this->hasAgreements()) {
            if (!\Mage::getStoreConfigFlag('checkout/options/enable_agreements')) {
                $agreements = array();
            } else {
                $agreements = \Mage::getModel('\Magento\Checkout\Model\Agreement')->getCollection()
                    ->addStoreFilter(\Mage::app()->getStore()->getId())
                    ->addFieldToFilter('is_active', 1);
            }
            $this->setAgreements($agreements);
        }
        return $this->getData('agreements');
    }
}
