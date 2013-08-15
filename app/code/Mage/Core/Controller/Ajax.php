<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Frontend ajax controller
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Controller_Ajax extends Mage_Core_Controller_Front_Action
{
    /**
     * Ajax action for inline translation
     *
     */
    public function translateAction ()
    {
        $translation = $this->getRequest()->getPost('translate');
        if (!is_array($translation)) {
            $translation = array($translation);
        }
        $area = $this->getRequest()->getPost('area');

        //filtering
        /** @var $filter Mage_Core_Model_Input_Filter_MaliciousCode */
        $filter = Mage::getModel('Mage_Core_Model_Input_Filter_MaliciousCode');
        foreach ($translation as &$item) {
            $item['custom'] = $filter->filter($item['custom']);
        }

        $response = Mage::helper('Mage_Core_Helper_Translate')->apply($translation, $area);
        $this->getResponse()->setBody($response);
        $this->setFlag('', self::FLAG_NO_POST_DISPATCH, true);
    }
}
