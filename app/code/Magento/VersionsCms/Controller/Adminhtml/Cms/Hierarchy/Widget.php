<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Admihtml Widget Controller for Hierarchy Node Link plugin
 *
 * @category   Magento
 * @package    Magento_VersionsCms
 */
class Magento_VersionsCms_Controller_Adminhtml_Cms_Hierarchy_Widget extends Magento_Adminhtml_Controller_Action
{
    /**
     * Chooser Source action
     */
    public function chooserAction()
    {
        $this->getResponse()->setBody(
            $this->_getTreeBlock()
                ->setScope($this->getRequest()->getParam('scope'))
                ->setScopeId((int)$this->getRequest()->getParam('scope_id'))
                ->getTreeHtml()
        );
    }

    /**
     * Tree block instance
     *
     * @return Magento_VersionsCms_Block_Adminhtml_Cms_Hierarchy_Widget_Chooser
     */
    protected function _getTreeBlock()
    {
        return $this->getLayout()->createBlock('Magento_VersionsCms_Block_Adminhtml_Cms_Hierarchy_Widget_Chooser', '', array(
            'data' => array(
                'id' => $this->getRequest()->getParam('uniq_id')
            )
        ));
    }
}
