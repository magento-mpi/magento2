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
 * @package     Enterprise_GiftRegistry
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Front end helper block to show giftregistry mark
 */
class Enterprise_GiftRegistry_Block_Product_View extends Mage_Catalog_Block_Product_View
{
    /**
     * Giftregistry param flag value in url option params
     * @var string
     */
    const FLAG = 'giftregistry';

    /**
     * Prepare layout
     *
     * @return Enterprise_GiftRegistry_Block_Product_View
     */
    protected function _prepareLayout()
    {
        $block = $this->getLayout()->getBlock('customize.button');
        if ($block && $this->_isGiftRegistryRedirect()) {
            $block->setTemplate('giftregistry/product/customize.phtml');
        }
        return parent::_prepareLayout();
    }

    public function __construct()
    {
        if ($this->_isGiftRegistryRedirect()) {
            $this->setTemplate('giftregistry/product/addtocart.phtml');
        } else {
            $this->setTemplate('catalog/product/view/addtocart.phtml');
        }

        parent::_construct();
    }
    /**
     * Return giftregistry add cart items url
     *
     * @return string
     */
    public function getAddToGiftregistryUrl()
    {
        return $this->getUrl('enterprise_giftregistry/index/cart',
            array('entity' => $this->getRequest()->getParam('entity')));
    }

    /**
     * Return gift registry redirect flag.
     *
     * @return bool
     */
    protected function _isGiftRegistryRedirect()
    {
        return $this->getRequest()->getParam('options') == self::FLAG;
    }
}
