<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * XmlConnect cms page controller
 *
 * @category    Mage
 * @package     Mage_Xmlconnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_CmsController extends Mage_XmlConnect_Controller_Action
{
    /**
     * Declare content type header
     *
     * @return null
     */
    public function preDispatch()
    {
        parent::preDispatch();
        $this->getResponse()->setHeader('Content-type', 'text/html; charset=UTF-8');
    }

    /**
     * Category list
     *
     * @return null
     */
    public function pageAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }
}
