<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Paygate
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Authorizenet Payment CC Types Source Model
 *
 * @category    Mage
 * @package     Mage_Paygate
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Paygate_Model_Authorizenet_Source_Cctype extends Mage_Payment_Model_Source_Cctype
{
    public function getAllowedTypes()
    {
        return array('VI', 'MC', 'AE', 'DI', 'OT');
    }
}
