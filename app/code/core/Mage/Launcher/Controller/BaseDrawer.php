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
 * Base Drawer controller
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Controller_BaseDrawer
    extends Mage_Backend_Controller_ActionAbstract
    implements Mage_Launcher_Controller_Drawer
{
    /**
     * Drawer Block Class Name, has to be set in all child classes
     *
     * @var string
     */
    protected $_drawerBlockName;

    /**
     * Tile Block Class Name, has to be set in all child classes
     *
     * @var string
     */
    protected $_tileBlockName;

    /**
     * @param Mage_Core_Controller_Request_Http $request
     * @param Mage_Core_Controller_Response_Http $response
     * @param string $areaCode
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Controller_Varien_Front $frontController
     * @param Mage_Core_Model_Layout_Factory $layoutFactory
     * @param array $data
     */
    public function __construct(
        Mage_Core_Controller_Request_Http $request,
        Mage_Core_Controller_Response_Http $response,
        $areaCode = null,
        Magento_ObjectManager $objectManager,
        Mage_Core_Controller_Varien_Front $frontController,
        Mage_Core_Model_Layout_Factory $layoutFactory,
        array $data = array()
    ) {
        parent::__construct($request, $response, $areaCode, $objectManager, $frontController, $layoutFactory, $data);

        if (isset($data['drawerBlockName'])) {
            $this->_drawerBlockName = $data['drawerBlockName'];
        }

        if (isset($data['tileBlockName'])) {
            $this->_tileBlockName = $data['tileBlockName'];
        }
    }

    /**
     * Get Drawer Block Name
     *
     * @return string
     */
    public function getDrawerBlockName() {
        return $this->_drawerBlockName;
    }

    /**
     * Set Drawer Block Name
     *
     * @return Mage_Launcher_Controller_BaseDrawer
     */
    public function setDrawerBlockName($drawerBlock) {
        $this->_drawerBlockName = $drawerBlock;
        return $this;
    }

    /**
     * Get Tile Block Name
     *
     * @return string
     */
    public function getTileBlockName() {
        return $this->_tileBlockName;
    }

    /**
     * Set Tile Block Name
     *
     * @return Mage_Launcher_Controller_BaseDrawer
     */
    public function setTileBlockName($tileBlock) {
        $this->_tileBlockName = $tileBlock;
        return $this;
    }

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

            /** @var $tileBlock Mage_Launcher_Block_Adminhtml_Tile */
            $tileBlock = $this->getLayout()
                ->createBlock($this->_tileBlockName);
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

            /** @var $drawerBlock Mage_Launcher_Block_Adminhtml_Drawer */
            $drawerBlock = $this->getLayout()
                ->createBlock($this->_drawerBlockName);
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
