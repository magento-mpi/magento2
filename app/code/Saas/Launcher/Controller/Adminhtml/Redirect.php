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
 * Redirect controller
 *
 * @category    Magento
 * @package     Saas_Launcher
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Saas_Launcher_Controller_Adminhtml_Redirect extends Magento_Backend_Controller_ActionAbstract
{
    /**
     * Index action
     */
    public function indexAction()
    {
        $idParam = $this->getRequest()->getParam('id');

        $link = $idParam ? Mage::getModel('Saas_Launcher_Model_LinkTracker')->load($idParam) : null;

        if (!$link || !$link->getId()) {
            return $this->_forward('noroute');
        }

        $link->setIsVisited(true);
        $link->save();

        return $this->_redirect($link->getUrl(), unserialize($link->getParams()));
    }
}
