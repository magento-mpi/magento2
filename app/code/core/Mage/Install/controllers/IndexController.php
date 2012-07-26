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
 *
 * @category   Mage
 * @package    Mage_Install
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Install_IndexController extends Mage_Install_Controller_Action
{

    /**
     * Dispatch event before action
     *
     * @return void
     */
    public function preDispatch()
    {
        $this->setFlag('', self::FLAG_NO_CHECK_INSTALLATION, true);
        if (!Mage::isInstalled()) {
            Mage::helper('Mage_Install_Helper_Data')->cleanVarFolder();
        }
        return parent::preDispatch();
    }

    /**
     * Index action
     */
    function indexAction()
    {
        $this->_forward('begin', 'wizard', 'install');
    }

}
