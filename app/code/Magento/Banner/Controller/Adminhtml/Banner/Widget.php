<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Banner
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog Product widgets controller for CMS WYSIWYG
 *
 * @category   Magento
 * @package    Magento_Banner
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Banner_Controller_Adminhtml_Banner_Widget extends Magento_Adminhtml_Controller_Action
{
    /**
     * Chooser Source action
     */
    public function chooserAction()
    {
        $uniqId = $this->getRequest()->getParam('uniq_id');

        $bannersGrid = $this->getLayout()->createBlock(
            'Magento_Banner_Block_Adminhtml_Widget_Chooser', '', array('data' => array('id' => $uniqId))
        );
        $html = $bannersGrid->toHtml();

        $this->getResponse()->setBody($html);
    }
}
