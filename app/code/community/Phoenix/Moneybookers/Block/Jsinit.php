<?php
/**
 * {license_notice}
 *
 * @category    Phoenix
 * @package     Phoenix_Moneybookers
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Phoenix_Moneybookers_Block_Jsinit extends Mage_Adminhtml_Block_Template
{
    /**
     * Include JS in head if section is moneybookers
     */
    protected function _prepareLayout()
    {
        $section = $this->getRequest()->getParam('section', false);
        if ($section == 'moneybookers') {
            $this->getLayout()
                ->getBlock('head')
                ->addJs('Phoenix_Moneybookers::activation.js');
        }
        parent::_prepareLayout();
    }

    /**
     * Print init JS script into body
     * @return string
     */
    protected function _toHtml()
    {
        $section = $this->getRequest()->getParam('section', false);
        if ($section == 'moneybookers') {
            return parent::_toHtml();
        } else {
            return '';
        }
    }
}
