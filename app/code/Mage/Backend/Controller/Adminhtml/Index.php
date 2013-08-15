<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Index backend controller
 *
 * @category    Mage
 * @package     Mage_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Controller_Adminhtml_Index extends Mage_Backend_Controller_ActionAbstract
{
    /**
     * Admin area entry point
     * Always redirects to the startup page url
     */
    public function indexAction()
    {
        $this->_redirect(Mage::getSingleton('Mage_Backend_Model_Url')->getStartupPageUrl());
    }
}
