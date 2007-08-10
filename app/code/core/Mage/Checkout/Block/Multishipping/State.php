<?php
/**
 * Multishipping checkout state
 *
 * @package     Mage
 * @subpackage  Checkout
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Checkout_Block_Multishipping_State extends Mage_Core_Block_Template
{
    public function getSteps()
    {
        return Mage::getSingleton('checkout/type_multishipping_state')->getSteps();
    }
}
