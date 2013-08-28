<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Magento_Backend view container block
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 * @deprecated is not used in code
 */

class Magento_Backend_Block_Widget_View_Container extends Magento_Backend_Block_Widget_Container
{
    protected $_objectId = 'id';

    protected $_blockGroup = 'Magento_Backend';

    protected $_template = 'Magento_Backend::widget/view/container.phtml';


    protected function _construct()
    {
        parent::_construct();

        $this->_addButton('back', array(
            'label'     => __('Back'),
            'onclick'   => 'window.location.href=\'' . $this->getUrl('*/*/') . '\'',
            'class'     => 'back',
        ));

        $this->_addButton('edit', array(
            'label'     => __('Edit'),
            'class'     => 'edit',
            'onclick'   => 'window.location.href=\'' . $this->getEditUrl() . '\'',
        ));

    }

    protected function _prepareLayout()
    {
        $blockName = $this->_blockGroup
            . '_Block_'
            . str_replace(' ', '_', ucwords(str_replace('_', ' ', $this->_controller)))
            . '_View_Plane';

        $this->setChild('plane', $this->getLayout()->createBlock($blockName));

        return parent::_prepareLayout();
    }

    public function getEditUrl()
    {
        return $this->getUrl('*/*/edit', array($this->_objectId => $this->getRequest()->getParam($this->_objectId)));
    }

    public function getViewHtml()
    {
        return $this->getChildHtml('plane');
    }

}
