<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Payment
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Payment\Model\Method;

class Ccsave extends \Magento\Payment\Model\Method\Cc
{
    protected $_code        = 'ccsave';
    protected $_canSaveCc   = true;
    protected $_formBlockType = '\Magento\Payment\Block\Form\Ccsave';
    protected $_infoBlockType = '\Magento\Payment\Block\Info\Ccsave';
}
