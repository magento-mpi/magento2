<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */

 /**
 * Enterprise attribute edit block
 *
 * @category   Magento
 * @package    Magento_Search
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Search\Block\Adminhtml\Catalog\Attribute;

class Edit extends \Magento\Backend\Block\Template
{
    /**
     * Search data
     *
     * @var \Magento\Search\Helper\Data
     */
    protected $_searchData = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Search\Helper\Data $searchData
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Search\Helper\Data $searchData,
        array $data = array()
    ) {
        $this->_searchData = $searchData;
        parent::__construct($context, $coreData, $data);
    }

    /**
     * Return true if third part search engine used
     *
     * @return boolean
     */
    public function isThirdPartSearchEngine()
    {
        return $this->_searchData->isThirdPartSearchEngine();
    }
}
