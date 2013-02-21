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
     * Save Origin Address Action
     */
    public function saveOriginAddressAction()
    {
        try {
            $data = $this->getRequest()->getParams();
            /** @var $tileModel Mage_Launcher_Model_Tile */
            $tileModel = Mage::getModel('Mage_Launcher_Model_TileFactory')->create('shipping');
            $saveHandler = $tileModel->getSaveHandler();
            $saveHandler->saveOriginAddress($data);
            $responseContent = $this->_composeAjaxResponseContent(Mage::helper('Mage_Launcher_Helper_Data')->__('Configuration has been successfully saved.'), true);
        } catch (Exception $e) {
            $responseContent = $this->_composeAjaxResponseContent(Mage::helper('Mage_Launcher_Helper_Data') ->__($e->getMessage()), false);
        }
        $this->getResponse()->setBody($responseContent);
    }

    /**
     * Save Shipping Method Action
     */
    public function saveShippingAction()
    {
        $responseContent = '';
        try {
            $data = $this->getRequest()->getParams();
            /** @var $tileModel Mage_Launcher_Model_Tile */
            $tileModel = Mage::getModel('Mage_Launcher_Model_TileFactory')->create('shipping');
            $saveHandler = $tileModel->getSaveHandler();
            $saveHandler->saveShippingMethod($data);
            $responseContent = $this->_composeAjaxResponseContent(Mage::helper('Mage_Launcher_Helper_Data')->__('Configuration has been successfully saved.'), true);
        } catch (Exception $e) {
            $responseContent = $this->_composeAjaxResponseContent(Mage::helper('Mage_Launcher_Helper_Data') ->__($e->getMessage()), false);
        }
        $this->getResponse()->setBody($responseContent);
    }
}
