<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Payment Profiles Iframe block
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Pbridge_Block_Payment_Profile extends Enterprise_Pbridge_Block_Iframe_Abstract
{
    /**
     * Default iframe height
     *
     * @var string
     */
    protected $_iframeHeight = '600';

    /**
     * Getter for Payment Profiles Iframe source URL.
     * Return Payment Bridge url with required parameters (such as merchant code, merchant key etc.)
     * Can include quote shipping and billing address if its required in payment processing
     *
     * @return string
     */
    public function getSourceUrl()
    {
        return Mage::helper('enterprise_pbridge')->getPaymentProfileUrl(
            array(
                'billing_address' => $this->_getAddressInfo(),
                'css_url'         => $this->getCssUrl(),
                'customer_id'     => $this->getCustomerIdentifier(),
                'customer_name'   => $this->getCustomerName(),
                'customer_email'  => $this->getCustomerEmail()
            )
        );
    }
}
