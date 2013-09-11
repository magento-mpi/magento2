<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Admin RMA create
 *
 * @category    Magento
 * @package     Magento_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Rma\Block\Adminhtml\Rma;

class Create extends \Magento\Adminhtml\Block\Widget\Form\Container
{
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_rma';
        $this->_mode = 'create';
        $this->_blockGroup = 'Magento_Rma';

        parent::_construct();

        $this->setId('magento_rma_rma_create');
        $this->removeButton('save');
        $this->removeButton('reset');
    }

    public function getHeaderHtml()
    {
        return $this->getLayout()->createBlock('\Magento\Rma\Block\Adminhtml\Rma\Create\Header')->toHtml();
    }
}
