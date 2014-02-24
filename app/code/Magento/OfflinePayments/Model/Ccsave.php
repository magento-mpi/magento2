<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\OfflinePayments\Model;

class Ccsave extends \Magento\Payment\Model\Method\Cc
{
    /**
     * @var string
     */
    protected $_code        = 'ccsave';

    /**
     * @var bool
     */
    protected $_canSaveCc   = true;

    /**
     * @var string
     */
    protected $_formBlockType = 'Magento\OfflinePayments\Block\Form\Ccsave';

    /**
     * @var string
     */
    protected $_infoBlockType = 'Magento\OfflinePayments\Block\Info\Ccsave';
}
