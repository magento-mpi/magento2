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
 * Adminhtml Catalog Product List Sortable allowed sortable attributes source
 *
 * @category   Enterprise
 * @package    Enterprise_Logging
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Logging_Model_System_Config_RotationList
{
    /**
     * Retrieve option values array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();
        $config = Mage::getConfig()->getNode('adminhtml/enterprise/logging/frequency');
        $children = $config->children();
        foreach($children as $frequence) {
            $options[] = array(
                'label' => Mage::helper('enterprise_logging')->__((string)$frequence->label),
                'value' => (string)$frequence->value,
            );
        }
        return $options;
    }
}