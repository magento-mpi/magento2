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
 * Base Drawer controller
 *
 * @category    Mage
 * @package     Saas_Launcher
 * @author      Magento Core Team <core@magentocommerce.com>
 *
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
class Saas_Launcher_Controller_BaseDrawer
    extends Magento_Backend_Controller_ActionAbstract
    implements Saas_Launcher_Controller_Drawer
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
     * @param Magento_Backend_Controller_Context $context
     * @param string $areaCode
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Controller_Context $context,
        $areaCode = null,
        array $data = array()
    ) {
        parent::__construct($context, $areaCode);

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
    public function getDrawerBlockName()
    {
        return $this->_drawerBlockName;
    }

    /**
     * Set Drawer Block Name
     *
     * @param string $drawerBlock
     * @return Saas_Launcher_Controller_BaseDrawer
     */
    public function setDrawerBlockName($drawerBlock)
    {
        $this->_drawerBlockName = $drawerBlock;
        return $this;
    }

    /**
     * Get Tile Block Name
     *
     * @return string
     */
    public function getTileBlockName()
    {
        return $this->_tileBlockName;
    }

    /**
     * Set Tile Block Name
     *
     * @param string $tileBlock
     * @return Saas_Launcher_Controller_BaseDrawer
     */
    public function setTileBlockName($tileBlock)
    {
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
            // Generate blocks before Refresh State to prevent DI preferences overloading
            $layout = $this->loadLayout();

            $data = $this->getRequest()->getParams();
            /** @var $tileModel Saas_Launcher_Model_Tile */
            $tileModel = Mage::getModel('Saas_Launcher_Model_TileFactory')->create($data['tileCode']);
            $tileModel->refreshState($data);

            /** @var $tileBlock Saas_Launcher_Block_Adminhtml_Tile */
            $tileBlock = $layout->getLayout()->getBlock($data['tileCode'] . '.tile');
            if (empty($tileBlock)) {
                /** @var $tileBlock Saas_Launcher_Block_Adminhtml_Tile */
                $tileBlock = $this->getLayout()->createBlock($this->_tileBlockName);
            }

            $tileBlock->setTile($tileModel);

            $responseContent = Mage::helper('Saas_Launcher_Helper_Data')->jsonEncode(
                $tileBlock->getResponseContent()
            );
        } catch (Exception $e) {
            $responseContent = $this->_composeAjaxResponseContent(
                Mage::helper('Saas_Launcher_Helper_Data')->__($e->getMessage()), false
            );
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
            /** @var $tileModel Saas_Launcher_Model_Tile */
            $tileModel = Mage::getModel('Saas_Launcher_Model_TileFactory')->create($tileCode);

            $layout = $this->loadLayout();
            /** @var $drawerBlock Saas_Launcher_Block_Adminhtml_Drawer */
            $drawerBlock = $layout->getLayout()->getBlock($tileCode . '.drawer');
            if (empty($drawerBlock)) {
                /** @var $drawerBlock Saas_Launcher_Block_Adminhtml_Drawer */
                $drawerBlock = $this->getLayout()->createBlock($this->_drawerBlockName);
            }

            $drawerBlock->setTile($tileModel);

            $responseContent = Mage::helper('Saas_Launcher_Helper_Data')->jsonEncode(
                $drawerBlock->getResponseContent()
            );
        } catch (Exception $e) {
            $responseContent = $this->_composeAjaxResponseContent(
                Mage::helper('Saas_Launcher_Helper_Data')->__($e->getMessage()), false
            );
        }
        $this->getResponse()->setBody($responseContent);
    }

    /**
     * Compose AJAX response content
     *
     * @param string $message
     * @param boolean $isSuccessful
     * @param array $additionalData
     * @return string
     */
    protected function _composeAjaxResponseContent($message, $isSuccessful, $additionalData = array())
    {
        $responseData = array('success' => $isSuccessful);
        if ($isSuccessful) {
            $responseData['message'] = $message;
        } else {
            $responseData['error_message'] = $message;
        }
        $responseData = array_merge($responseData, $additionalData);

        return Mage::helper('Saas_Launcher_Helper_Data')->jsonEncode($responseData);
    }
}
