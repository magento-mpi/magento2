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
 * Tax Drawer controller
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Adminhtml_Storelauncher_Tax_DrawerController extends Mage_Backend_Controller_ActionAbstract
    implements Mage_Launcher_Controller_Drawer
{
    /**
     * Retrieve Drawer Content Action
     */
    public function loadAction()
    {
        $ruleModel = Mage::getModel('Mage_Tax_Model_Calculation_Rule');
        Mage::register('tax_rule', $ruleModel);

        try {
            $tileCode = $this->getRequest()->getParam('tileCode');
            $tileModel = Mage::getModel('Mage_Launcher_Model_Tile')->loadByCode($tileCode);
            $layout = $this->loadLayout();
            /** @var $drawerBlock Mage_Launcher_Block_Adminhtml_Storelauncher_Tax_Drawer */
            $drawerBlock = $layout->getLayout()
                ->getBlock('tax_drawer');
            $drawerBlock->setTile($tileModel);

            $responseContent = Mage::helper('Mage_Launcher_Helper_Data')->jsonEncode(
                $drawerBlock->getResponseContent()
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
     * Drawer Save Action
     */
    public function saveAction()
    {
        try {
            $data = $this->getRequest()->getParams();
            /** @var $tileModel Mage_Launcher_Model_Tile */
            $tileModel = Mage::getModel('Mage_Launcher_Model_Tile')->loadByCode($data['tileCode']);
            $tileModel->refreshState($data);

            /** @var $tileBlock Mage_Launcher_Block_Adminhtml_Storelauncher_Tax_Tile */
            $tileBlock = $this->getLayout()
                ->createBlock('Mage_Launcher_Block_Adminhtml_Storelauncher_Tax_Tile');
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

}
