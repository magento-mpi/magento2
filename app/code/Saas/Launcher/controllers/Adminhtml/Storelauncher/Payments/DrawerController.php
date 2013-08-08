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
 * Payments Drawer controller
 *
 * @category    Mage
 * @package     Saas_Launcher
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Saas_Launcher_Adminhtml_Storelauncher_Payments_DrawerController
    extends Saas_Launcher_Controller_BaseDrawer
{
    /**
     * Save Payment Method Action
     */
    public function savePaymentAction()
    {
        $responseContent = '';
        try {
            $data = $this->getRequest()->getParams();
            /** @var $tileModel Saas_Launcher_Model_Tile */
            $tileModel = Mage::getModel('Saas_Launcher_Model_TileFactory')->create('payments');
            $saveHandler = $tileModel->getSaveHandler();
            $saveHandler->savePaymentMethod($data);
            $responseContent = $this->_composeAjaxResponseContent(
                __('Configuration has been successfully saved.'), true
            );
        } catch (Exception $e) {
            $responseContent = $this->_composeAjaxResponseContent(
                __($e->getMessage()), false
            );
        }
        $this->getResponse()->setBody($responseContent);
    }
}
