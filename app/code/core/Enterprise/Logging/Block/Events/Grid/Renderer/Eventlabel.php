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
 * @category   Mage
 * @package    Mage_Import
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

/**
 * Import type column renderer
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Logging_Block_Events_Grid_Renderer_Eventlabel extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

     /**
     * Replacing event_code by event_label from config
     *
     * @param Varien_Object row   row with 'event_code' item
     *
     * @return string - replaced ip value.
     */

    public function render(Varien_Object $row)
    {
        $code = $row->getData($this->getColumn()->getIndex());
        $node = Mage::getConfig()->getNode('enterprise/logging/events');
        $label = $code;
        foreach($node->children() as $child) {
            if($code == $child->getName()) {
                $label = $child->label;
            }
        }

    	return $label;
    }
}