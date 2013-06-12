<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Abstract payment block
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Pbridge_Block_Checkout_Payment_Abstract extends Enterprise_Pbridge_Block_Payment_Form_Abstract
{
    /**
     * Return 3D validation flag
     *
     * @return bool
     */
    public function is3dSecureEnabled()
    {
        if ($this->hasMethod() && $this->getMethod()->is3dSecureEnabled()) {
            return true;
        }
        return parent::is3dSecureEnabled();
    }
}
