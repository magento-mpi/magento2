<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml catalog inventory "Minimum Qty Allowed in Shopping Cart" field
 *
 * @category   Mage
 * @package    Mage_CatalogInventory
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogInventory_Block_Adminhtml_Form_Field_Minsaleqty extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    /**
     * Prepare to render
     */
    protected function _prepareToRender()
    {
        $groupColumnRenderer = $this->getLayout()->createBlock('cataloginventory/adminhtml_form_field_customergroup');
        $groupColumnRenderer->setClass('customer_group_select');
        $groupColumnRenderer->setExtraParams('style="width:120px" rel="#{customer_group_id}"');
        $this->addColumn('customer_group_id', array(
            'label' => Mage::helper('customer')->__('Customer Group'),
            'renderer' => $groupColumnRenderer,
        ));
        $this->addColumn('min_sale_qty', array(
            'label' => Mage::helper('cataloginventory')->__('Minimum Qty'),
            'style' => 'width:100px',
        ));
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('cataloginventory')->__('Add Minimum Qty');
    }

    /**
     * Render block HTML with js add-on to choose select options
     *
     * @return string
     */
    public function _toHtml()
    {
        return parent::_toHtml()
            . ' <script type="text/javascript">'
            . ' var selects = $$(".customer_group_select");'
            . ' for(var j = 0; j < selects.length; j++){'
            . '     for(var i = 0; i < selects[j].options.length; i++){'
            . '         if(selects[j].options[i].value == selects[j].attributes.getNamedItem("rel").value){'
            . '             selects[j].options[i].selected = true;'
            . '             break;'
            . '         }'
            . '     }'
            . ' }'
            . ' </script>'
        ;
    }
}
