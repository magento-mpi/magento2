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
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml review grid item renderer for item type
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Review_Grid_Renderer_Type extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
	{
		if (is_null($row->getCustomerId())) {
			return Mage::helper('review')->__('Guest');
		} elseif ($row->getCustomerId() == 0) {
			return Mage::helper('review')->__('Administrator');
		} elseif ($row->getCustomerId() > 0) {
			return Mage::helper('review')->__('Customer');
		}
//		return ($row->getCustomerId() ? Mage::helper('review')->__('Customer') : Mage::helper('review')->__('Guest'));
	}
}// Class Mage_Adminhtml_Block_Review_Grid_Renderer_Type END