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
 * Customer giftregistry list block
 *
 * @category   Enterprise
 * @package    Enterprise_GiftRegistry
 */
class Enterprise_GiftRegistry_Block_Customer_List extends Enterprise_Enterprise_Block_Customer_Account_Dashboard
{
    /**
     * Return list of giftregistrys
     *
     * @return Enterprise_GiftRegistry_Model_Mysql4_GiftRegistry_Collection
     */
    public function getEntityCollection()
    {
        if (!$this->hasEntityCollection()) { // case is GiftregistryCollection !!!
            $this->setData('entity_collection', Mage::getModel('enterprise_giftregistry/entity')->getCollection()
                ->addOrder('registry_id', Varien_Data_Collection::SORT_ORDER_DESC)
                ->loadByCustomerId(Mage::getSingleton('customer/session')->getCustomerId())
            );
        }
        return $this->_getData('entity_collection');
    }

    /**
     * Return status text for giftregistry
     *
     * @param Enterprise_GiftRegistry_Model_GiftRegistry $giftregistry
     * @return string
     */
    public function getStatusText($giftregistry)
    {
        return 'GiftRegistry STATUS TEXT (DEVELOPMENT) ';
        return Mage::getSingleton('enterprise_giftregistry/source_entity_status')
            ->getOptionText($giftregistry->getStatus());
    }
    /**
     * Instantiate Pagination
     *
     * @return Enterprise_GiftRegistry_Block_Customer_List
     */
    protected function _prepareLayout()
    {
        $pager = $this->getLayout()->createBlock('page/html_pager', 'giftregistry.list.pager')
            ->setCollection($this->getEntityCollection())->setIsOutputRequired(false);
        $this->setChild('pager', $pager);
        return parent::_prepareLayout();
    }

    public function getTitle($item)
    {
        return $item->getData('title');
    }

    public function getRegistryId($item)
    {
        return $item->getData('title');
    }

    public function getMessage($item)
    {
        return $item->getData('title');
    }
}
