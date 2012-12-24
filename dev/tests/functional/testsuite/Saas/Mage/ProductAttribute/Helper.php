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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    tests
 * @package     selenium
 * @subpackage  tests
 * @author      Magento Goext Team <DL-Magento-Team-Goext@corp.ebay.com>
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Saas_Mage_ProductAttribute_Helper extends Core_Mage_ProductAttribute_Helper
{
    /**
     * Delete attributes
     * @param array
     */
    public function deleteAttributes(array $attributesData)
    {
        foreach($attributesData as $attrData) {
            $searchData = $this->loadDataSet('ProductAttributes', 'attribute_search_data',
                array(
                    'attribute_code'  => $attrData['attribute_code'],
                    'attribute_label' => $attrData['admin_title'],
                )
            );
            $this->openAttribute($searchData);
            $this->clickButtonAndConfirm('delete_attribute', 'delete_confirm_message');
            //Verifying
            $this->assertMessagePresent('success', 'success_deleted_attribute');
        }
    }
}