<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Saas_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Drawer controller interface
 *
 * This interface has to be implemented by all drawer controllers
 *
 * @category    Magento
 * @package     Saas_Launcher
 * @author      Magento Core Team <core@magentocommerce.com>
 */
interface Saas_Launcher_Controller_Drawer
{
    /**
     * Drawer Save Action
     */
    public function saveAction();

    /**
     * Retrieve Drawer Content Action
     */
    public function loadAction();
}
