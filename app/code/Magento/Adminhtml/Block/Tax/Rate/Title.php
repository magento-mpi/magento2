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
namespace Magento\Adminhtml\Block\Tax\Rate;

class Title extends \Magento\Core\Block\Template
{
    protected $_titles;

    protected $_template = 'tax/rate/title.phtml';

    public function getTitles()
    {
        if (is_null($this->_titles)) {
            $this->_titles = array();
            $titles = \Mage::getSingleton('Magento\Tax\Model\Calculation\Rate')->getTitles();
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
            $stores = \Mage::getModel('Magento\Core\Model\Store')
                ->getResourceCollection()
                ->setLoadDefault(false)
                ->load();
            $this->setData('stores', $stores);
        }
        return $stores;
    }
}
