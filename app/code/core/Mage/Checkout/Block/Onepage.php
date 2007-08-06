<?php
/**
 * Onepage checkout block
 *
 * @package    Mage
 * @subpackage Checkout
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Checkout_Block_Onepage extends Mage_Checkout_Block_Onepage_Abstract
{
    public function getSteps()
    {
        return $this->getCheckout()->getStepData();
    }
    
    public function getActiveStep()
    {
        return $this->getCheckout()->getLastAllowedStep();
    }
}