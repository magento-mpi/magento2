<?php
/**
 * {license}
 *
 * @category    Mage
 * @package     Mage_Oauth
 */

/**
 * OAuth authorization block with auth buttons
 *
 * @category   Mage
 * @package    Mage_Oauth
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Oauth_Block_Authorize_Button extends Mage_Oauth_Block_Authorize_ButtonBaseAbstract
{
    /**
     * Retrieve confirm authorization url path
     *
     * @return string
     */
    public function getConfirmUrlPath()
    {
        return 'oauth/authorize/confirm';
    }

    /**
     * Retrieve reject authorization url path
     *
     * @return string
     */
    public function getRejectUrlPath()
    {
        return 'oauth/authorize/reject';
    }
}
