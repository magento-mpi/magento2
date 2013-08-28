<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Search Customer Model
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Model_Search_Customer extends Magento_Object
{
    /**
     * Adminhtml data
     *
     * @var Magento_Adminhtml_Helper_Data
     */
    protected $_adminhtmlData = null;

    /**
     * Constructor
     *
     * By default is looking for first argument as array and assigns it as object
     * attributes This behavior may change in child classes
     *
     * @param Magento_Adminhtml_Helper_Data $adminhtmlData
     */
    public function __construct(
        Magento_Adminhtml_Helper_Data $adminhtmlData
    ) {
        $this->_adminhtmlData = $adminhtmlData;
    }

    /**
     * Load search results
     *
     * @return Magento_Adminhtml_Model_Search_Customer
     */
    public function load()
    {
        $result = array();
        if (!$this->hasStart() || !$this->hasLimit() || !$this->hasQuery()) {
            $this->setResults($result);
            return $this;
        }

        $collection = Mage::getResourceModel('Magento_Customer_Model_Resource_Customer_Collection')
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
            $result[] = array(
                'id'            => 'customer/1/'.$customer->getId(),
                'type'          => __('Customer'),
                'name'          => $customer->getName(),
                'description'   => $customer->getCompany(),
                'url' => $this->_adminhtmlData->getUrl('*/customer/edit', array('id' => $customer->getId())),
            );
        }

        $this->setResults($result);

        return $this;
    }
}
