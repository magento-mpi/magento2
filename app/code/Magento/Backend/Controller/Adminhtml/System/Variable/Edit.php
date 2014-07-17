<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml\System\Variable;

class Edit extends \Magento\Backend\Controller\Adminhtml\System\Variable
{
    /**
     * Edit Action
     *
     * @return void
     */
    public function execute()
    {
        $variable = $this->_initVariable();

        $this->_title->add($variable->getId() ? $variable->getCode() : __('New Custom Variable'));

        $this->_initLayout()->_addContent(
            $this->_view->getLayout()->createBlock('Magento\Backend\Block\System\Variable\Edit')
        )->_addJs(
            $this->_view->getLayout()->createBlock(
                'Magento\Framework\View\Element\Template',
                '',
                array('data' => array('template' => 'Magento_Backend::system/variable/js.phtml'))
            )
        );
        $this->_view->renderLayout();
    }
}
