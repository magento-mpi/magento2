<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Install
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Install index controller
 */
class Mage_Install_IndexController extends Mage_Install_Controller_Action
{

    /**
     * Dispatch event before action
     */
    public function preDispatch()
    {
        $this->setFlag('', self::FLAG_NO_CHECK_INSTALLATION, true);
        if (!Mage::isInstalled()) {
            foreach (glob(Mage::getBaseDir(Mage_Core_Model_Dir::VAR_DIR) . '/*', GLOB_ONLYDIR) as $dir) {
                Varien_Io_File::rmdirRecursive($dir);
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
