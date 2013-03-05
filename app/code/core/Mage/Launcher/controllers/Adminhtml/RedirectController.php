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
 * Redirect controller
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Adminhtml_RedirectController extends Mage_Backend_Controller_ActionAbstract
{
    /**
     * Index action
     */
    public function indexAction()
    {
        $id = $this->getRequest()->getParam('id');

        $link = $id ? Mage::getModel('Mage_Launcher_Model_LinkTracker')->load($id) : null;

        if (!$link || !$link->getId()) {
            return $this->_forward('noroute');
        }

        $link->setIsVisited(true);
        $link->save();

        return $this->_redirect($link->getUrl(), unserialize($link->getParams()));
    }
}
