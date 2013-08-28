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
 * Admin RMA create
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Enterprise_Rma_Block_Adminhtml_Rma_Create extends Magento_Adminhtml_Block_Widget_Form_Container
{
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_rma';
        $this->_mode = 'create';
        $this->_blockGroup = 'Enterprise_Rma';

        parent::_construct();

        $this->setId('enterprise_rma_rma_create');
        $this->removeButton('save');
        $this->removeButton('reset');
    }

    public function getHeaderHtml()
    {
        return $this->getLayout()->createBlock('Enterprise_Rma_Block_Adminhtml_Rma_Create_Header')->toHtml();
    }
}
