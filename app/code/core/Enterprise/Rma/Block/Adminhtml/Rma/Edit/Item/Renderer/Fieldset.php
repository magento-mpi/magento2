<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Form fieldset default renderer
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */
class Enterprise_Rma_Block_Adminhtml_Rma_Edit_Item_Renderer_Fieldset
    extends Mage_Adminhtml_Block_Widget_Form_Renderer_Fieldset
{
    protected function _construct()
    {
        $this->setTemplate('edit/item/renderer/fieldset.phtml');
    }
}
