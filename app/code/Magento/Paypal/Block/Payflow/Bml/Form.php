<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Paypal\Block\Payflow\Bml;

/** @todo methodCode should be set in constructor, than this form should be eliminated */
class Form extends \Magento\Paypal\Block\Bml\Form
{
    /**
     * Payment method code
     * @var string
     */
    protected $_methodCode = Config::METHOD_WPP_PE_BML;
}
