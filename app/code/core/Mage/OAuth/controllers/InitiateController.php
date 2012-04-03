<?php
/**
 * {license}
 *
 * @category    Mage
 * @package     Mage_OAuth
 */

/**
 * oAuth initiate controller
 *
 * @category    Mage
 * @package     Mage_OAuth
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_OAuth_InitiateController extends Mage_Core_Controller_Front_Action
{
    /**
     * Dispatch event before action
     *
     * @return void
     */
    public function preDispatch()
    {
        $this->setFlag('', self::FLAG_NO_START_SESSION, 1);
        $this->setFlag('', self::FLAG_NO_CHECK_INSTALLATION, 1);
        $this->setFlag('', self::FLAG_NO_COOKIES_REDIRECT, 0);
        $this->setFlag('', self::FLAG_NO_PRE_DISPATCH, 1);

        parent::preDispatch();
    }

    /**
     * Index action. Receive initiate request and response OAuth token
     */
    public function indexAction()
    {
        /** @var $server Mage_OAuth_Model_Server */
        $server = Mage::getModel('Mage_OAuth_Model_Server');

        $server->initiateToken();
    }
}
