<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Search Customer Model
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Model_Search_Customer extends Magento_Object
{
    /**
     * Load search results
     *
     * @return Mage_Adminhtml_Model_Search_Customer
     */
    public function load()
    {
        $arr = array();

        if (!$this->hasStart() || !$this->hasLimit() || !$this->hasQuery()) {
            $this->setResults($arr);
            return $this;
        }
        $collection = Mage::getResourceModel('Mage_Customer_Model_Resource_Customer_Collection')
            ->addNameToSelect()
            ->joinAttribute('company', 'customer_address/company', 'default_billing', null, 'left')
            ->addAttributeToFilter(array(
                array('attribute'=>'firstname', 'like' => $this->getQuery().'%'),
                array('attribute'=>'lastname', 'like'  => $this->getQuery().'%'),
                array('attribute'=>'company', 'like'   => $this->getQuery().'%'),
            ))
            ->setPage(1, 10)
            ->load();

        foreach ($collection->getItems() as $customer) {
            $arr[] = array(
                'id'            => 'customer/1/'.$customer->getId(),
                'type'          => __('Customer'),
                'name'          => $customer->getName(),
                'description'   => $customer->getCompany(),
                'url' => Mage::helper('Mage_Adminhtml_Helper_Data')->getUrl(
                    '*/customer/edit',
                    array(
                        'id' => $customer->getId()
                    )
                ),
            );
        }

        $this->setResults($arr);

        return $this;
    }
}
