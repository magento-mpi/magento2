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
 * Homepage Drawer controller
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Adminhtml_Promotestore_Homepage_DrawerController
    extends Mage_Backend_Controller_ActionAbstract
    implements Mage_Launcher_Controller_Drawer
{
    /**
     * Drawer Save Action
     */
    public function saveAction()
    {
        $responseContent = '';
        try {
            $data = $this->getRequest()->getParams();
            /** @var $tileModel Mage_Launcher_Model_Tile */
            $tileModel = Mage::getModel('Mage_Launcher_Model_Tile')->loadByCode($data['tileCode']);
            $tileModel->refreshState($data);

            /** @var $tileBlock Mage_Launcher_Block_Adminhtml_Promotestore_Homepage_Tile */
            $tileBlock = $this->getLayout()
                ->createBlock('Mage_Launcher_Block_Adminhtml_Promotestore_Homepage_Tile');
            $tileBlock->setTile($tileModel);

            $responseContent = Mage::helper('Mage_Launcher_Helper_Data')->jsonEncode(
                $tileBlock->getResponseContent()
            );
        } catch (Exception $e) {
            $responseContent = Mage::helper('Mage_Launcher_Helper_Data')->jsonEncode(array(
                'success' => false,
                'error_message' => Mage::helper('Mage_Launcher_Helper_Data')->__($e->getMessage())
            ));
        }
        $this->getResponse()->setBody($responseContent);
    }

    /**
     * Retrieve Drawer Content Action
     */
    public function loadAction()
    {
        $responseContent = '';
        try {
            $tileCode = $this->getRequest()->getParam('tileCode');
            $tileModel = Mage::getModel('Mage_Launcher_Model_Tile')->loadByCode($tileCode);
            $drawerBlock = $this->getLayout()
                ->createBlock('Mage_Launcher_Block_Adminhtml_Promotestore_Homepage_Drawer');
            $drawerBlock->setTile($tileModel);

            $responseContent = Mage::helper('Mage_Launcher_Helper_Data')->jsonEncode(
                $drawerBlock->getResponseContent()
            );
        } catch (Exception $e) {
            $responseContent = Mage::helper('Mage_Launcher_Helper_Data')->jsonEncode(array(
                'success' => false,
                'error_message' => Mage::helper('Mage_Launcher_Helper_Data') ->__($e->getMessage())
            ));
        }
        $this->getResponse()->setBody($responseContent);
    }
}
