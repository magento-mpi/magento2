<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer storage
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 *
 */
namespace Magento\ImportExport\Model\Resource\Customer;

class Storage
{
    /**
     * Flag to not load collection more than one time
     *
     * @var bool
     */
    protected $_isCollectionLoaded = false;

    /**
     * Customer collection
     *
     * @var \Magento\Customer\Model\Resource\Customer\Collection
     */
    protected $_customerCollection;

    /**
     * Existing customers information. In form of:
     *
     * [customer e-mail] => array(
     *    [website id 1] => customer_id 1,
     *    [website id 2] => customer_id 2,
     *           ...       =>     ...      ,
     *    [website id n] => customer_id n,
     * )
     *
     * @var array
     */
    protected $_customerIds = array();

    /**
     * Number of items to fetch from db in one query
     *
     * @var int
     */
    protected $_pageSize;

    /**
     * Collection by pages iterator
     *
     * @var \Magento\ImportExport\Model\Resource\CollectionByPagesIterator
     */
    protected $_byPagesIterator;

    /**
     * Constructor
     *
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        $this->_customerCollection = isset($data['customer_collection']) ? $data['customer_collection']
            : \Mage::getResourceModel('\Magento\Customer\Model\Resource\Customer\Collection');
        $this->_pageSize = isset($data['page_size']) ? $data['page_size'] : 0;
        $this->_byPagesIterator = isset($data['collection_by_pages_iterator']) ? $data['collection_by_pages_iterator']
            : \Mage::getResourceModel('\Magento\ImportExport\Model\Resource\CollectionByPagesIterator');
    }

    /**
     * Load needed data from customer collection
     */
    public function load()
    {
        if ($this->_isCollectionLoaded == false) {
            $collection = clone $this->_customerCollection;
            $collection->removeAttributeToSelect();
            $tableName = $collection->getResource()->getEntityTable();
            $collection->getSelect()->from($tableName, array('entity_id', 'website_id', 'email'));

            $this->_byPagesIterator->iterate($this->_customerCollection, $this->_pageSize,
                array(array($this, 'addCustomer'))
            );

            $this->_isCollectionLoaded = true;
        }
    }

    /**
     * Add customer to array
     *
     * @param \Magento\Object|\Magento\Customer\Model\Customer $customer
     * @return \Magento\ImportExport\Model\Resource\Customer\Storage
     */
    public function addCustomer(\Magento\Object $customer)
    {
        $email = strtolower(trim($customer->getEmail()));
        if (!isset($this->_customerIds[$email])) {
            $this->_customerIds[$email] = array();
        }
        $this->_customerIds[$email][$customer->getWebsiteId()] = $customer->getId();

        return $this;
    }

    /**
     * Get customer id
     *
     * @param string $email
     * @param int $websiteId
     * @return bool|int
     */
    public function getCustomerId($email, $websiteId)
    {
        // lazy loading
        $this->load();

        if (isset($this->_customerIds[$email][$websiteId])) {
            return $this->_customerIds[$email][$websiteId];
        }

        return false;
    }
}
