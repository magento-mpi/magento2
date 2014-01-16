<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Block for tabs in extension info
 *
 * @category    Magento
 * @package     Magento_Connect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Connect\Block\Adminhtml\Extension\Custom\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
    * Constructor
    */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('connect_extension_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Create Extension Package'));
    }

    /**
    * Set tabs
    *
    * @return \Magento\Connect\Block\Adminhtml\Extension\Custom\Edit\Tabs
    */
    protected function _beforeToHtml()
    {
//        $this->addTab('package', array(
//            'label'     => __('Package Info'),
//            'content'   => $this->_getTabHtml('package'),
//            'active'    => true,
//        ));
//
//        $this->addTab('release', array(
//            'label'     => __('Release Info'),
//            'content'   => $this->_getTabHtml('release'),
//        ));
//
//        $this->addTab('maintainers', array(
//            'label'     => __('Authors'),
//            'content'   => $this->_getTabHtml('authors'),
//        ));
//
//        $this->addTab('depends', array(
//            'label'     => __('Dependencies'),
//            'content'   => $this->_getTabHtml('depends'),
//        ));
//
//        $this->addTab('contents', array(
//            'label'     => __('Contents'),
//            'content'   => $this->_getTabHtml('contents'),
//        ));
//
//        $this->addTab('load', array(
//            'label'     => __('Load local Package'),
//            'class'     => 'ajax',
//            'url'       => $this->getUrl('adminhtml/*/loadtab', array('_current' => true)),
//        ));

        return parent::_beforeToHtml();
    }

    /**
    * Retrieve HTML for tab
    *
    * @param string $tab
    * @return string
    */
    protected function _getTabHtml($tab)
    {
//        $classNameParts = explode('_', $tab);
//        foreach ($classNameParts as $key => $part) {
//            $classNameParts[$key] = ucfirst($part);
//        }
//        return $this->getLayout()
//            ->createBlock('Magento\Connect\Block\Adminhtml\Extension\Custom\Edit\Tab\\' . implode('_', $classNameParts))
//            ->initForm()
//            ->toHtml();
    }

}
