<?php
/**
 * Customer Grid Collection
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Resource\Order\Customer;

class Collection extends \Magento\Customer\Model\Resource\Customer\Collection
{
    /**
     * @return \Magento\Sales\Model\Resource\Order\Customer\Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addNameToSelect()
            ->addAttributeToSelect('email')
            ->addAttributeToSelect('created_at')
            ->joinAttribute('billing_postcode', 'customer_address/postcode', 'default_billing', null, 'left')
            ->joinAttribute('billing_city', 'customer_address/city', 'default_billing', null, 'left')
            ->joinAttribute('billing_telephone', 'customer_address/telephone', 'default_billing', null, 'left')
            ->joinAttribute('billing_regione', 'customer_address/region', 'default_billing', null, 'left')
            ->joinAttribute('billing_country_id', 'customer_address/country_id', 'default_billing', null, 'left')
            ->joinField('store_name', 'store', 'name', 'store_id=store_id', null, 'left');
        return $this;
    }
}
