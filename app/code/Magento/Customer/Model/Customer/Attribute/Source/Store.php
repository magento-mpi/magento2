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
 * Customer store attribute source
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Customer\Model\Customer\Attribute\Source;

class Store extends \Magento\Eav\Model\Entity\Attribute\Source\Table
{
    /**
     * @var \Magento\Core\Model\System\Store
     */
    protected $_store;

    /**
     * @var \Magento\Core\Model\Resource\Store\CollectionFactory
     */
    protected $_storesFactory;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\Option\CollectionFactory $attrOptCollFactory
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\OptionFactory $attrOptionFactory
     * @param \Magento\Core\Model\System\Store $store
     * @param \Magento\Core\Model\Resource\Store\CollectionFactory $storesFactory
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Eav\Model\Resource\Entity\Attribute\Option\CollectionFactory $attrOptCollFactory,
        \Magento\Eav\Model\Resource\Entity\Attribute\OptionFactory $attrOptionFactory,
        \Magento\Core\Model\System\Store $store,
        \Magento\Core\Model\Resource\Store\CollectionFactory $storesFactory
    ) {
        parent::__construct($coreData, $attrOptCollFactory, $attrOptionFactory);
        $this->_store = $store;
        $this->_storesFactory = $storesFactory;
    }

    public function getAllOptions()
    {
        if (!$this->_options) {
            $collection = $this->_createStoresCollection();
            if ('store_id' == $this->getAttribute()->getAttributeCode()) {
                $collection->setWithoutDefaultFilter();
            }
            $this->_options = $this->_store->getStoreValuesForForm();
            if ('created_in' == $this->getAttribute()->getAttributeCode()) {
                array_unshift($this->_options, array('value' => '0', 'label' => __('Admin')));
            }
        }
        return $this->_options;
    }

    public function getOptionText($value)
    {
        if(!$value)$value ='0';
        $isMultiple = false;
        if (strpos($value, ',')) {
            $isMultiple = true;
            $value = explode(',', $value);
        }

        if (!$this->_options) {
            $collection = $this->_createStoresCollection();
            if ('store_id' == $this->getAttribute()->getAttributeCode()) {
                $collection->setWithoutDefaultFilter();
            }
            $this->_options = $collection->load()->toOptionArray();
            if ('created_in' == $this->getAttribute()->getAttributeCode()) {
                array_unshift($this->_options, array('value' => '0', 'label' => __('Admin')));
            }
        }

        if ($isMultiple) {
            $values = array();
            foreach ($value as $val) {
                $values[] = $this->_options[$val];
            }
            return $values;
        }
        else {
            return $this->_options[$value];
        }
        return false;
    }

    /**
     * @return \Magento\Core\Model\Resource\Store\Collection
     */
    protected function _createStoresCollection()
    {
        return $this->_storesFactory->create();
    }
}
