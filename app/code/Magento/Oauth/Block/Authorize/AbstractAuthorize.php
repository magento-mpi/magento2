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
 * OAuth abstract authorization block
 *
 * @category   Magento
 * @package    Magento_Oauth
 * @author     Magento Core Team <core@magentocommerce.com>
 * @method string getToken()
 * @method \Magento\Oauth\Block\AbstractAuthorizeBase setToken() setToken(string $token)
 * @method boolean getIsSimple()
 * @method \Magento\Oauth\Block\Authorize\Button setIsSimple() setIsSimple(boolean $flag)
 * @method boolean getHasException()
 * @method \Magento\Oauth\Block\AbstractAuthorizeBase setIsException() setHasException(boolean $flag)
 * @method boolean getVerifier()
 * @method \Magento\Oauth\Block\AbstractAuthorizeBase setVerifier() setVerifier(string $verifier)
 * @method boolean getIsLogged()
 * @method \Magento\Oauth\Block\AbstractAuthorizeBase setIsLogged() setIsLogged(boolean $flag)
 */
namespace Magento\Oauth\Block\Authorize;

abstract class AbstractAuthorize extends \Magento\Core\Block\Template
{
    /**
     * Consumer model
     *
     * @var \Magento\Oauth\Model\Consumer
     */
    protected $_consumer;

    /**
     * Get consumer instance by token value
     *
     * @return \Magento\Oauth\Model\Consumer
     */
    public function getConsumer()
    {
        if (null === $this->_consumer) {
            /** @var $token \Magento\Oauth\Model\Token */
            $token = \Mage::getModel('Magento\Oauth\Model\Token');
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
