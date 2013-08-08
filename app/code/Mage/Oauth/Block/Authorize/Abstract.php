<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Oauth
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * OAuth abstract authorization block
 *
 * @category   Mage
 * @package    Mage_Oauth
 * @author     Magento Core Team <core@magentocommerce.com>
 * @method string getToken()
 * @method Mage_Oauth_Block_AuthorizeBaseAbstract setToken() setToken(string $token)
 * @method boolean getIsSimple()
 * @method Mage_Oauth_Block_Authorize_Button setIsSimple() setIsSimple(boolean $flag)
 * @method boolean getHasException()
 * @method Mage_Oauth_Block_AuthorizeBaseAbstract setIsException() setHasException(boolean $flag)
 * @method boolean getVerifier()
 * @method Mage_Oauth_Block_AuthorizeBaseAbstract setVerifier() setVerifier(string $verifier)
 * @method boolean getIsLogged()
 * @method Mage_Oauth_Block_AuthorizeBaseAbstract setIsLogged() setIsLogged(boolean $flag)
 */
abstract class Mage_Oauth_Block_Authorize_Abstract extends Magento_Core_Block_Template
{
    /**
     * Helper
     *
     * @var Mage_Oauth_Helper_Data
     */
    protected $_helper;

    /**
     * Consumer model
     *
     * @var Mage_Oauth_Model_Consumer
     */
    protected $_consumer;

    /**
     * Constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_helper = Mage::helper('Mage_Oauth_Helper_Data');
    }

    /**
     * Get consumer instance by token value
     *
     * @return Mage_Oauth_Model_Consumer
     */
    public function getConsumer()
    {
        if (null === $this->_consumer) {
            /** @var $token Mage_Oauth_Model_Token */
            $token = Mage::getModel('Mage_Oauth_Model_Token');
            $token->load($this->getToken(), 'token');
            $this->_consumer = $token->getConsumer();
        }
        return $this->_consumer;
    }

    /**
     * Get absolute path to template
     *
     * Load template from adminhtml/default area flag is_simple is set
     *
     * @return string
     */
    public function getTemplateFile()
    {
        if (!$this->getIsSimple()) {
            return parent::getTemplateFile();
        }

        //load base template from admin area
        $params = array(
            '_relative' => true,
            'area'     => 'adminhtml',
            'package'  => 'default'
        );
        return $this->_viewFileSystem->getFilename($this->getTemplate(), $params);
    }
}
