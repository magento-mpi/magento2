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
 * BusinessInfo Drawer controller
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Adminhtml_Storelauncher_Businessinfo_DrawerController extends Mage_Backend_Controller_ActionAbstract
    implements Mage_Launcher_Controller_Drawer
{
    const TILE_HEADER = 'Store Info';

    /**
     * Drawer Save Action
     */
    public function saveAction()
    {
        try {
            $data = $this->getRequest()->getParams();
            /** @var $config Mage_Backend_Model_Config */
            $config = Mage::getModel('Mage_Backend_Model_Config');
            $sections = array('general', 'trans_email', 'shipping');
            $sectionData = $this->_prepareData($data);

            foreach ($sections as $section) {
                if (!empty($sectionData[$section])) {
                    $config->setSection($section)
                        ->setGroups($sectionData[$section])
                        ->save();
                }
            }

            $responseContent = Mage::helper('Mage_Launcher_Helper_Data')->jsonEncode(array(
                'success' => true,
                'error_message' => ''
            ));
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
        try {
            $tileCode = $this->getRequest()->getParam('tileCode');
            $tileModel = Mage::getModel('Mage_Launcher_Model_Tile')->loadByCode($tileCode);
            $drawerBlock = $this->getLayout()
                ->createBlock('Mage_Launcher_Block_Adminhtml_Storelauncher_Businessinfo_Drawer');
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
}
