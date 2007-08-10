<?php
/**
 * Multishipping checkout success information
 *
 * @package     Mage
 * @subpackage  Checkout
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Checkout_Block_Multishipping_Success extends Mage_Checkout_Block_Multishipping_Abstract
{
    public function getContinueUrl()
    {
        return Mage::getBaseUrl();
    }
}
