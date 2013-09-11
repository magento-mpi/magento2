<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Adminhtml Google Content Item Type Country Renderer
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GoogleShopping\Block\Adminhtml\Types\Renderer;

class Country
    extends \Magento\Adminhtml\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Renders Google Content Item Id
     *
     * @param   \Magento\Object $row
     * @return  string
     */
    public function render(\Magento\Object $row)
    {
        $iso = $row->getData($this->getColumn()->getIndex());
        return \Mage::getSingleton('Magento\GoogleShopping\Model\Config')->getCountryInfo($iso, 'name');
    }
}
