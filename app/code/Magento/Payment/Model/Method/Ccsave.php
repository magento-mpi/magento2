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
    protected $_formBlockType = 'Magento\Payment\Block\Form\Ccsave';

    /**
     * @var string
     */
    protected $_infoBlockType = 'Magento\Payment\Block\Info\Ccsave';
}
