<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Cybersource
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Cybersource_Model_Config extends Mage_Payment_Model_Config
{
    /**
     * Retrieve array of credit card types
     *
     * @return array
    */
    public function getCcTypes()
    {
        $pTypes = parent::getCcTypes();
        $types = array();
        $added = false;
        foreach ($pTypes as $code => $name) {
             if ($code=='OT') {
                $added = true;
                $this->addExtraCcTypes(&$types);
            }
            $types[$code] = $name;
        }
        if (!$added) {
            $this->addExtraCcTypes(&$types);
        }
        return $types;
    }

    public function addExtraCcTypes($types)
    {
        $types['JCB'] = Mage::helper('cybersource')->__('JCB');
        $types['LASER'] = Mage::helper('cybersource')->__('Laser');
        $types['UATP'] = Mage::helper('cybersource')->__('UATP');
        $types['MCI'] = Mage::helper('cybersource')->__('Maestro (International)');
        $types[Mage_Cybersource_Model_Soap::CC_CARDTYPE_SS] = Mage::helper('cybersource')->__('Switch/Solo/Maestro(UK Domestic)');

    }

}