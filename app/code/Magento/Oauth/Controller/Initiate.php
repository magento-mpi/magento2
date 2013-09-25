<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Oauth
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * oAuth initiate controller
 *
 * @category    Magento
 * @package     Magento_Oauth
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Oauth_Controller_Initiate extends Magento_Core_Controller_Front_Action
{
    /**
     * Server model factory
     *
     * @var Magento_Oauth_Model_ServerFactory
     */
    protected $_serverFactory = null;

    public function __construct(
        Magento_Core_Controller_Varien_Action_Context $context,
        Magento_Oauth_Model_ServerFactory $serverFactory
    ) {
        parent::__construct($context);
        $this->_serverFactory = $serverFactory;
    }

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
        $this->_serverFactory->create()->initiateToken();
    }
}
