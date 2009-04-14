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

class Enterprise_Logging_Block_Events_Grid_Filter_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select
{
    /**
     * Build options list for filter
     */
    public function _getOptions() {
        $options = array(array('value' => '', 'label' => 'All actions'));
        $resource = Mage::getResourceModel('enterprise_logging/event');
        $collection = $resource->getActions();
        foreach($collection as $action)
          $options[] = array('value' => $action->getId(), 'label' => $action->getName());
        return $options;
    }
    
    /**
     * returns value
     */
    public function getCondition()
    {
    	return $this->getValue();
    }
}
