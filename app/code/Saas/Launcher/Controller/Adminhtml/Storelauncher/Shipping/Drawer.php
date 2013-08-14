<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Saas_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Shipping Drawer controller
 *
 * @category    Mage
 * @package     Saas_Launcher
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Saas_Launcher_Controller_Adminhtml_Storelauncher_Shipping_Drawer
    extends Saas_Launcher_Controller_BaseDrawer
{
    /**
     * Save Origin Address Action
     */
    public function saveOriginAddressAction()
    {
        try {
            $data = $this->getRequest()->getParams();
            /** @var $tileModel Saas_Launcher_Model_Tile */
            $tileModel = Mage::getModel('Saas_Launcher_Model_TileFactory')->create('shipping');
            $saveHandler = $tileModel->getSaveHandler();
            $saveHandler->saveOriginAddress($data);
            $responseContent = $this->_composeAjaxResponseContent(
                Mage::helper('Saas_Launcher_Helper_Data')->__('Configuration has been successfully saved.'), true
            );
        } catch (Exception $e) {
            $responseContent = $this->_composeAjaxResponseContent(
                Mage::helper('Saas_Launcher_Helper_Data')->__($e->getMessage()), false
            );
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
            /** @var $tileModel Saas_Launcher_Model_Tile */
            $tileModel = Mage::getModel('Saas_Launcher_Model_TileFactory')->create('shipping');
            $saveHandler = $tileModel->getSaveHandler();
            $saveHandler->saveShippingMethod($data);
            $responseContent = $this->_composeAjaxResponseContent(
                Mage::helper('Saas_Launcher_Helper_Data')->__('Configuration has been successfully saved.'), true
            );
        } catch (Exception $e) {
            $responseContent = $this->_composeAjaxResponseContent(
                Mage::helper('Saas_Launcher_Helper_Data')->__($e->getMessage()), false
            );
        }
        $this->getResponse()->setBody($responseContent);
    }
}
