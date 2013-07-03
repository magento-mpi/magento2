<?php
/**
 * {license_notice}
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Cardgate_Model_Gateway_Ideal extends Mage_Cardgate_Model_Gateway_Abstract
{
    /**
     * Cardgate Payment Method Code
     *
     * @var string
     */
    protected $_code  = 'cardgate_ideal';

    /**
     * Cardgate Payment Model Code
     *
     * @var string
     */
    protected $_model = 'ideal';

    /**
     * Cardgate Form Block class name
     *
     * @var string
     */
    protected $_formBlockType = 'Mage_Cardgate_Block_Form_Ideal';
}
