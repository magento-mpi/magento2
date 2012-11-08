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
        $this->loadLayout()
            ->renderLayout();
    }
}
