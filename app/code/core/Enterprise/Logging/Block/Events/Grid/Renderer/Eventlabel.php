<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Enterprise
 * @package    Enterprise_Logging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
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
        return Mage::getSingleton('enterprise_logging/event')->getLabel($code);
    }
}