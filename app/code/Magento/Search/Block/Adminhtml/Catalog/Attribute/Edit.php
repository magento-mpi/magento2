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

class Edit extends \Magento\Adminhtml\Block\Template
{
    /**
     * Return true if third part search engine used
     *
     * @return boolean
     */
    public function isThirdPartSearchEngine()
    {
        return \Mage::helper('Magento\Search\Helper\Data')->isThirdPartSearchEngine();
    }
}
