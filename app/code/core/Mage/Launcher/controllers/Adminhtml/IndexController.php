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
 * Launcher controller
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Adminhtml_IndexController extends Mage_Backend_Controller_ActionAbstract
{
    /**
     * Index action
     */
    public function indexAction()
    {
        /** @var $page Mage_Launcher_Model_Page */
        $page = Mage::getModel('Mage_Launcher_Model_Page')
            ->loadByCode('store_launcher');
        $layout = $this->loadLayout();
        $layout->getLayout()
            ->getBlock('landing.page')->setPage($page);
        $layout->renderLayout();
    }
}
