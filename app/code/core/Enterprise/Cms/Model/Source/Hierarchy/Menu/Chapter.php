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
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * CMS Hierarchy Menu source model for Chapter/Section options
 *
 * @category   Enterprise
 * @package    Enterprise_Cms
 */
class Enterprise_Cms_Model_Source_Hierarchy_Menu_Chapter
{
    /**
     * Return options for Chapter/Section meta links
     *
     * @return array
     */
    public function toOptionArray()
    {
        $helper = Mage::helper('enterprise_cms');
        $options = array(
            array('label' => $helper->__('No'), 'value' => ''),
            array('label' => $helper->__('Chapter'), 'value' => 'chapter'),
            array('label' => $helper->__('Section'), 'value' => 'section'),
            array('label' => $helper->__('Both'), 'value' => 'both'),
        );

        return $options;
    }
}
