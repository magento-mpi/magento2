<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Mage_Backend view container block
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 * @deprecated is not used in code
 */

class Mage_Backend_Block_Widget_View_Container extends Mage_Backend_Block_Widget_Container
{
    protected $_objectId = 'id';

    protected $_blockGroup = 'Mage_Backend';

    protected $_template = 'Mage_Backend::widget/view/container.phtml';


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
