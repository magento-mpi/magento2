<?php
/**
 * {license}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 */

/**
 * OAuth admin authorization block
 *
 * @category   Mage
 * @package    Mage_OAuth
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_OAuth_Block_Adminhtml_OAuth_Authorize extends Mage_OAuth_Block_AuthorizeBaseAbstract
{
    /**
     * Retrieve admin form posting url
     *
     * @return string
     */
    public function getPostActionUrl()
    {
        $params = array();
        if ($this->getIsSimple()) {
            $params['simple'] = 1;
        }
        return $this->getUrl('adminhtml/index/login', $params);
    }

    /**
     * Get form identity label
     *
     * @return string
     */
    public function getIdentityLabel()
    {
        return $this->__('User Name');
    }

    /**
     * Get form identity label
     *
     * @return string
     */
    public function getFormTitle()
    {
        return $this->__('Log in as admin');
    }

    /**
     * Retrieve reject application authorization URL
     *
     * @return string
     */
    public function getRejectUrlPath()
    {
        return 'adminhtml/oAuth_authorize/reject';
    }
}
