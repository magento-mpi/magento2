<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend ajax controller
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Controller_Ajax extends Mage_Adminhtml_Controller_Action
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
        /** @var $filter Mage_Core_Model_Input_Filter_MaliciousCode */
        $filter = Mage::getModel('Mage_Core_Model_Input_Filter_MaliciousCode');
        foreach ($translation as &$item) {
            $item['custom'] = $filter->filter($item['custom']);
        }

        echo Mage::helper('Mage_Core_Helper_Translate')->apply($translation, $area);
        exit();
    }
}
