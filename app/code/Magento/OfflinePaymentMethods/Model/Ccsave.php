<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\OfflinePaymentMethods\Model;

class Ccsave extends \Magento\Payment\Model\Method\Cc
{
    protected $_code        = 'ccsave';
    protected $_canSaveCc   = true;
    protected $_formBlockType = 'Magento\OfflinePaymentMethods\Block\Form\Ccsave';
    protected $_infoBlockType = 'Magento\OfflinePaymentMethods\Block\Info\Ccsave';
}
