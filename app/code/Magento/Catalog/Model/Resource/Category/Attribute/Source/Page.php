<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog category landing page attribute source
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Model\Resource\Category\Attribute\Source;

class Page extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * Return all block options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = \Mage::getResourceModel('Magento\Cms\Model\Resource\Block\Collection')
                ->load()
                ->toOptionArray();
            array_unshift($this->_options, array('value' => '', 'label' => __('Please select a static block.')));
        }
        return $this->_options;
    }
}
