<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Payments Drawer controller
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Adminhtml_Storelauncher_Payments_DrawerController
    extends Mage_Launcher_Controller_BaseDrawer
{
    /**
     * Save Payment Method Action
     */
    public function savePaymentAction()
    {
        $data = $this->getRequest()->getParams();
        /** @var $tileModel Mage_Launcher_Model_Tile */
        $tileModel = Mage::getModel('Mage_Launcher_Model_Tile')->loadByCode('payments');
        $saveHandler = $tileModel->getSaveHandler();
        //@todo call $saveHandler->savePaymentMethod($data) to save data and retrieve result

        $responseContent = Mage::helper('Mage_Launcher_Helper_Data')->jsonEncode(array(
            'success' => true,
        ));
        $this->getResponse()->setBody($responseContent);
    }
}
