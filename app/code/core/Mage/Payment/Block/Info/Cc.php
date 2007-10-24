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
 * @package    Mage_Payment
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Payment_Block_Info_Cc extends Mage_Core_Block_Template
{
    protected function _construct()
    {
        $this->setTemplate('payment/info/cc.phtml');
        parent::_construct();
    }

    public function getCcTypes()
    {
        return array(
            ''=>__('Please select credit card type'),
            'AE'=>__('American Express'),
            'VI'=>__('Visa'),
            'MC'=>__('Master Card'),
            'DI'=>__('Discover'),
        );
    }

    public function getCcTypeName($type)
    {
    	$types = $this->getCcTypes();
    	return isset($types[$type]) ? $types[$type] : $type;
    }
}