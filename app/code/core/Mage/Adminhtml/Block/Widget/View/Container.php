<?php
/**
 * Adminhtml view container block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Widget_View_Container extends Mage_Adminhtml_Block_Widget_Container
{
    protected $_objectId = 'id';

    public function __construct()
    {
        parent::__construct();

        $this->setTemplate('widget/view/container.phtml');

        $this->_addButton('back', array(
            'label'     => __('Back'),
            'onclick'   => 'window.location.href=\'' . Mage::getUrl('*/*/') . '\'',
            'class'     => 'back',
        ));

        $this->_addButton('edit', array(
            'label'     => __('Edit'),
            'class'     => 'edit',
            'onclick'   => 'window.location.href=\'' . $this->getEditUrl() . '\'',
        ));

    }

    protected function _initChildren()
    {
        parent::_initChildren();
        $this->setChild('plane', $this->getLayout()->createBlock('adminhtml/' . $this->_controller . '_view_plane'));
        return $this;
    }

    public function getEditUrl()
    {
        return Mage::getUrl('*/*/edit', array($this->_objectId => $this->getRequest()->getParam($this->_objectId)));
    }

    public function getViewHtml()
    {
        return $this->getChildHtml('plane');
    }

}
