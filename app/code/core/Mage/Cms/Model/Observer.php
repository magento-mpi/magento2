<?php
/**
 * CMS Observer
 *
 * @file        Page.php
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski (hacki) alexander@varien.com
 */

class Mage_Cms_Model_Observer
{

    public function noRoute($observer)
    {
        $observer->getEvent()->getStatus()
            ->setLoaded(true)
            ->setForwardModule('Mage_Cms')
            ->setForwardController('Index')
            ->setForwardAction('noRoute');
    }

}

// ft:php
// fileformat:unix
// tabstop:4
?>
