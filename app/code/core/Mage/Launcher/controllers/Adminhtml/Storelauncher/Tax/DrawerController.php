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
    /** Tile Header */
    const TILE_HEADER = 'Tax Rules';

    /**
     * Retrieve Drawer Content Action
     */
    public function loadAction()
    {
        $ruleModel  = Mage::getModel('Mage_Tax_Model_Calculation_Rule');
        Mage::register('tax_rule', $ruleModel);

        try {
            $tileCode = $this->getRequest()->getParam('tileCode');
            $tileModel = Mage::getModel('Mage_Launcher_Model_Tile')->loadByCode($tileCode);
            $layout = $this->loadLayout();
            $drawerBlock = $layout->getLayout()
                ->getBlock('tax_drawer');
            $drawerBlock->setTile($tileModel);

            $responseContent = Mage::helper('Mage_Launcher_Helper_Data')->jsonEncode(array(
                'success' => true,
                'error_message' => '',
                'tile_code' => $drawerBlock->getTileCode(),
                'tile_content' => $drawerBlock->toHtml(),
                'tile_header' => Mage::helper('Mage_Launcher_Helper_Data')->__(self::TILE_HEADER)
            ));
        } catch (Exception $e) {
            $responseContent = Mage::helper('Mage_Launcher_Helper_Data')->jsonEncode(array(
                'success' => false,
                'error_message' => Mage::helper('Mage_Launcher_Helper_Data') ->__($e->getMessage())
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
            $tileCode = $data['tileCode'];
            $tileBlock = $this->getLayout()
                ->createBlock('Mage_Launcher_Block_Adminhtml_Storelauncher_Tax_Tile');

            //@TODO: Code below has to be moved to Model level
            // Load Tile when Data were saved and Tile possibly has changed it's state
            $tileModel = Mage::getModel('Mage_Launcher_Model_Tile')->loadByCode($tileCode);
            $tileStateResolver = $tileModel->getStateResolver();
            $tileState = $tileStateResolver->isTileComplete()
                ? Mage_Launcher_Model_Tile::STATE_COMPLETE
                : Mage_Launcher_Model_Tile::STATE_TODO;
            $tileModel->setState($tileState);
            $tileModel->save();
            $tileBlock->setTile($tileModel);

            //@TODO: Code below has to be moved to Block level
            $tileContent = $tileBlock->toHtml();
            $responseContent = Mage::helper('Mage_Launcher_Helper_Data')->jsonEncode(array(
                'success' => true,
                'error_message' => '',
                'tile_code' => $tileCode,
                'tile_state' => $tileBlock->getTileState(),
                'tile_content' => $tileContent
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
