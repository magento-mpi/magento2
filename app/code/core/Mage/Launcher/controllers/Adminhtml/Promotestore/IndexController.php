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
 * Promote Your Store controller
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Adminhtml_Promotestore_IndexController extends Mage_Backend_Controller_ActionAbstract
{
    /**
     * Index action
     */
    public function indexAction()
    {
        $layout = $this->loadLayout();
        $layout->getLayout();
        $layout->renderLayout();
    }
}
