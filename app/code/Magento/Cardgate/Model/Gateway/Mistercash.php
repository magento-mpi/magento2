<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cardgate
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @category   Magento
 * @package    Magento_Cardgate
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Cardgate_Model_Gateway_Mistercash extends Magento_Cardgate_Model_Gateway_Abstract
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
