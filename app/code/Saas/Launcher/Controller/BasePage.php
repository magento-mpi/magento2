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
 * Base page controller
 *
 * @category    Mage
 * @package     Saas_Launcher
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Saas_Launcher_Controller_BasePage extends Mage_Backend_Controller_ActionAbstract
{
    /**
     * Index Action
     */
    public function indexAction()
    {
        $layout = $this->loadLayout();
        $layout->getLayout();
        $this->_setActiveMenu('Magento_Adminhtml::dashboard');
        $layout->renderLayout();
    }
}
