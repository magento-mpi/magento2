<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_CatalogRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Flag stores status about availability not applied catalog price rules
 *
 * @category    Mage
 * @package     Mage_CatalogRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogRule_Model_Flag extends Magento_Core_Model_Flag
{
    /**
     * Flag code
     *
     * @var string
     */
    protected $_flagCode = 'catalog_rules_dirty';
}
