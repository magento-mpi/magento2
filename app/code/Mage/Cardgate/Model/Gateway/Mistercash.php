<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Cardgate
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @category   Mage
 * @package    Mage_Cardgate
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Cardgate_Model_Gateway_Mistercash extends Mage_Cardgate_Model_Gateway_Abstract
{
    /**
     * Cardgate Payment Method Code
     *
     * @var string
     */
    protected $_code  = 'cardgate_mistercash';

    /**
     * Cardgate Payment Model Code
     *
     * @var string
     */
    protected $_model = 'mistercash';
}
