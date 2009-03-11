<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Enterprise
 * @package    Enterprise_Logging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

class Enterprise_Logging_Block_Events_Grid_Renderer_User extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Render username from username column, not user_id
     * We must use user_id, for 'index' in grid, to make 
     * filter work.
     */
    public function render(Varien_Object $row)
    {
    	return $row->getData('username');
    }

}