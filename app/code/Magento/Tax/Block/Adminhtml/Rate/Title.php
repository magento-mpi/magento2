<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Tax Rate Titles Renderer
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Tax\Block\Adminhtml\Rate;

class Title extends \Magento\Core\Block\Template
{
    protected $_titles;

    protected $_template = 'rate/title.phtml';

    /**
     * @var \Magento\Tax\Model\Calculation\Rate
     */
    protected $_rate;

    /**
     * @var \Magento\Core\Model\StoreFactory
     */
    protected $_storeFactory;

    /**
     * @param \Magento\Core\Model\StoreFactory $storeFactory
     * @param \Magento\Tax\Model\Calculation\Rate $rate
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\StoreFactory $storeFactory,
        \Magento\Tax\Model\Calculation\Rate $rate,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
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
