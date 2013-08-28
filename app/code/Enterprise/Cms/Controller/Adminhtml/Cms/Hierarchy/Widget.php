<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Admihtml Widget Controller for Hierarchy Node Link plugin
 *
 * @category   Enterprise
 * @package    Enterprise_Cms
 */
class Enterprise_Cms_Controller_Adminhtml_Cms_Hierarchy_Widget extends Magento_Adminhtml_Controller_Action
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
     * @return Enterprise_Cms_Block_Adminhtml_Cms_Hierarchy_Widget_Chooser
     */
    protected function _getTreeBlock()
    {
        return $this->getLayout()->createBlock('Enterprise_Cms_Block_Adminhtml_Cms_Hierarchy_Widget_Chooser', '', array(
            'data' => array(
                'id' => $this->getRequest()->getParam('uniq_id')
            )
        ));
    }
}
