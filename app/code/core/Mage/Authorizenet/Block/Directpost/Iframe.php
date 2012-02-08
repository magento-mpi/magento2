<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Authorizenet
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * DirectPost iframe block
 *
 * @category   Mage
 * @package    Mage_Authorizenet
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Authorizenet_Block_Directpost_Iframe extends Mage_Core_Block_Template
{
    /**
     * Get params
     *
     * @return array
     */
    public function getParams()
    {
        if ($params = Mage::registry(Mage_Authorizenet_Directpost_PaymentController::REGISTER_FORM_PARAMS_KEY)) {
            return $params;
        }
        return array();
    }
}
