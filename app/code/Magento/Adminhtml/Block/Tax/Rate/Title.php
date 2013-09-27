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
 * Tax Rate Titles Renderer
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Tax_Rate_Title extends Magento_Core_Block_Template
{
    protected $_titles;

    protected $_template = 'tax/rate/title.phtml';

    /**
     * @var Magento_Tax_Model_Calculation_Rate
     */
    protected $_rate;

    /**
     * @var Magento_Core_Model_StoreFactory
     */
    protected $_storeFactory;

    /**
     * @param Magento_Core_Model_StoreFactory $storeFactory
     * @param Magento_Tax_Model_Calculation_Rate $rate
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_StoreFactory $storeFactory,
        Magento_Tax_Model_Calculation_Rate $rate,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_rate = $rate;
        $this->_storeFactory = $storeFactory;
        parent::__construct($coreData, $context, $data);
    }

    public function getTitles()
    {
        if (is_null($this->_titles)) {
            $this->_titles = array();
            $titles = $this->_rate->getTitles();
            foreach ($titles as $title) {
                $this->_titles[$title->getStoreId()] = $title->getValue();
            }
            foreach ($this->getStores() as $store) {
                if (!isset($this->_titles[$store->getId()])) {
                    $this->_titles[$store->getId()] = '';
                }
            }
        }
        return $this->_titles;
    }

    public function getStores()
    {
        $stores = $this->getData('stores');
        if (is_null($stores)) {
            $stores = $this->_storeFactory->create()
                ->getResourceCollection()
                ->setLoadDefault(false)
                ->load();
            $this->setData('stores', $stores);
        }
        return $stores;
    }
}
