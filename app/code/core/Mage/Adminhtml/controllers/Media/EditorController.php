<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Media library editor controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Media_EditorController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->_addContent(
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_Media_Editor')
        );
        $this->renderLayout();
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('Mage_Backend_Model_Auth_Session')->isAllowed('media');
    }
}
