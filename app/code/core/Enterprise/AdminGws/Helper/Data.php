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
 * @package    Enterprise_AdminGws
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Admin GWS helper
 *
 */
class Enterprise_AdminGws_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Transform comma-separeated ids string into array
     *
     * @param mixed $ids
     * @return mixed
     */
    public function explodeIds($ids, $separator = ',')
    {
        if (empty($ids) && $ids !== 0 && $ids !== '0') {
            return array();
        }
        if (!is_array($ids)) {
            return explode($separator, $ids);
        }
        return $ids;
    }
}
