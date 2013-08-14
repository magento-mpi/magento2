<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend ajax controller
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Controller_Ajax extends Magento_Adminhtml_Controller_Action
{
    /**
     * Ajax action for inline translation
     *
     */
    public function translateAction()
    {
        $translation = $this->getRequest()->getPost('translate');
        $area = $this->getRequest()->getPost('area');

        //filtering
        /** @var $filter Magento_Core_Model_Input_Filter_MaliciousCode */
        $filter = Mage::getModel('Magento_Core_Model_Input_Filter_MaliciousCode');
        foreach ($translation as &$item) {
            $item['custom'] = $filter->filter($item['custom']);
        }

        echo Mage::helper('Magento_Core_Helper_Translate')->apply($translation, $area);
        exit();
    }
}
