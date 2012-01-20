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
 * @package     Enterprise_PageCache
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Placeholder container for catalog product related block
 */
class Enterprise_PageCache_Model_Container_CatalogProductRelated
    extends Enterprise_PageCache_Model_Container_CatalogProductAbstract
{
    /**
     * Get Items Block Alias
     *
     * @return string
     */
    protected function _getItemsBlockAlias()
    {
        return 'catalog.product.related.item';
    }

    /**
     * Get Items Block Template Path
     *
     * @return string
     */
    protected function _getItemsBlockTemplatePath()
    {
        return 'targetrule/catalog/product/list/related/item.phtml';
    }

    /**
     * Get CachedId
     *
     * @return string
     */
    protected function _getCacheId()
    {
        return md5('CONTAINER_CATALOG_PRODUCT_RELATED_enterprise_targetrule/catalog_product_list_related');
    }
}
