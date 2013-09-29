<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Customer Address Attribute Tab Block
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CustomerCustomAttributes\Block\Adminhtml\Customer\Address\Attribute\Edit;

class Tabs
    extends \Magento\Adminhtml\Block\Widget\Tabs
{
    /**
     * Initialize edit tabs
     *
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('magento_customercustomattributes_address_attribute_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Attribute Information'));
    }
}
