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
 * Shipping Drawer controller
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Adminhtml_Storelauncher_Shipping_DrawerController
    extends Mage_Launcher_Controller_BaseDrawer
{
    /**
     * Drawer Save Action
     */
    public function saveOriginAddressAction()
    {
        try {
            $data = $this->getRequest()->getParams();
            /** @var $tileModel Mage_Launcher_Model_Tile */
            $tileModel = Mage::getModel('Mage_Launcher_Model_Tile')->loadByCode('shipping');
            $saveHandler = $tileModel->getSaveHandler();
            $saveHandler->saveOriginAddress($data);

            $responseContent = Mage::helper('Mage_Launcher_Helper_Data')->jsonEncode(array(
                'success' => true,
                'message' => Mage::helper('Mage_Launcher_Helper_Data')->__('Configuration has been successfully saved.')
            ));
        } catch (Exception $e) {
            $responseContent = Mage::helper('Mage_Launcher_Helper_Data')->jsonEncode(array(
                'success' => false,
                'error_message' => Mage::helper('Mage_Launcher_Helper_Data')->__($e->getMessage())
            ));
        }
        $this->getResponse()->setBody($responseContent);
    }
}
