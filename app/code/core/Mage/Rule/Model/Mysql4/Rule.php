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
 * @package    Mage_Rule
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Quote rule mysql4 resource model
 *
 * @category   Mage
 * @package    Mage_Rule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Rule_Model_Mysql4_Rule extends Mage_Core_Model_Mysql4_Abstract
{
	protected function _construct()
	{
		$this->_init('rule/rule', 'rule_id');
	}
}