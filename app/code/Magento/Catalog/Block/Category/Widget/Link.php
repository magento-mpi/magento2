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
 * Widget to display link to the category
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Catalog\Block\Category\Widget;

class Link
    extends \Magento\Catalog\Block\Widget\Link
{
    /**
     * Initialize entity model
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_entityResource = \Mage::getResourceSingleton('Magento\Catalog\Model\Resource\Category');
    }
}
