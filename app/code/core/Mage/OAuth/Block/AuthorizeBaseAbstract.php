<?php
/**
 * {license}
 *
 * @category    Mage
 * @package     Mage_OAuth
 */

/**
 * OAuth authorization block
 *
 * @category   Mage
 * @package    Mage_OAuth
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_OAuth_Block_AuthorizeBaseAbstract extends Mage_OAuth_Block_Authorize_Abstract
{
    /**
     * Retrieve user authorize form posting url
     *
     * @return string
     */
    abstract public function getPostActionUrl();

    /**
     * Retrieve reject authorization url
     *
     * @return string
     */
    public function getRejectUrl()
    {
        $url = $this->getUrl($this->getRejectUrlPath() . ($this->getIsSimple() ? 'Simple' : ''),
            array('_query' => array('oauth_token' => $this->getToken())));
        return $url;
    }

    /**
     * Retrieve reject URL path
     *
     * @return string
     */
    abstract public function getRejectUrlPath();

    /**
     * Get form identity label
     *
     * @return string
     */
    abstract public function getIdentityLabel();

    /**
     * Get form identity label
     *
     * @return string
     */
    abstract public function getFormTitle();
}
