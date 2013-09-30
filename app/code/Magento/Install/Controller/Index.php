<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Install
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Install index controller
 */
class Magento_Install_Controller_Index extends Magento_Install_Controller_Action
{
    /**
     * Core directory model
     *
     * @var Magento_Core_Model_Dir
     */
    protected $_coreDir;

    /**
     * @param Magento_Core_Controller_Varien_Action_Context $context
     * @param Magento_Core_Model_Config_Scope $configScope
     * @param Magento_Core_Model_View_DesignInterface $viewDesign
     * @param Magento_Core_Model_Theme_CollectionFactory $collectionFactory
     * @param Magento_Core_Model_App $app
     * @param Magento_Core_Model_App_State $appState
     * @param Magento_Core_Model_Dir $coreDir
     */
    public function __construct(
        Magento_Core_Controller_Varien_Action_Context $context,
        Magento_Core_Model_Config_Scope $configScope,
        Magento_Core_Model_View_DesignInterface $viewDesign,
        Magento_Core_Model_Theme_CollectionFactory $collectionFactory,
        Magento_Core_Model_App $app,
        Magento_Core_Model_App_State $appState,
        Magento_Core_Model_Dir $coreDir
    ) {
        parent::__construct($context, $configScope, $viewDesign, $collectionFactory, $app, $appState);
        $this->_coreDir = $coreDir;
    }

    /**
     * Dispatch event before action
     */
    public function preDispatch()
    {
        $this->setFlag('', self::FLAG_NO_CHECK_INSTALLATION, true);
        if (!$this->_appState->isInstalled()) {
            foreach (glob($this->_coreDir->getDir(Magento_Core_Model_Dir::VAR_DIR) . '/*', GLOB_ONLYDIR) as $dir) {
                Magento_Io_File::rmdirRecursive($dir);
            }
        }
        parent::preDispatch();
    }

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->_forward('begin', 'wizard', 'install');
    }
}
