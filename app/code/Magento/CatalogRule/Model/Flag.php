<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Flag stores status about availability not applied catalog price rules
 *
 * @category    Magento
 * @package     Magento_CatalogRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_CatalogRule_Model_Flag extends Magento_Core_Model_Flag
{
    /**
     * Flag code
     *
     * @var string
     */
    protected $_flagCode = 'catalog_rules_dirty';
}
