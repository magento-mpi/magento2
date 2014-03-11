<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\VersionsCms\Controller\Adminhtml\Cms\Hierarchy;

/**
 * Admihtml Widget Controller for Hierarchy Node Link plugin
 *
 * @category   Magento
 * @package    Magento_VersionsCms
 */
class Widget extends \Magento\Backend\App\Action
{
    /**
     * Chooser Source action
     *
     * @return void
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
     * @return \Magento\VersionsCms\Block\Adminhtml\Cms\Hierarchy\Widget\Chooser
     */
    protected function _getTreeBlock()
    {
        return $this->_view->getLayout()->createBlock('Magento\VersionsCms\Block\Adminhtml\Cms\Hierarchy\Widget\Chooser', '', array(
            'data' => array(
                'id' => $this->getRequest()->getParam('uniq_id')
            )
        ));
    }
}
