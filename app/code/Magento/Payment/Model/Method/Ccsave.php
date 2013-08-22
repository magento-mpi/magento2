<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Payment
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Payment_Model_Method_Ccsave extends Magento_Payment_Model_Method_Cc
{
    protected $_code        = 'ccsave';
    protected $_canSaveCc   = true;
    protected $_formBlockType = 'Magento_Payment_Block_Form_Ccsave';
    protected $_infoBlockType = 'Magento_Payment_Block_Info_Ccsave';
}
