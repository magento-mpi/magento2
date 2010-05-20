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
 * Gift registry frontend search controller
 */
class Enterprise_GiftRegistry_SearchController extends Enterprise_Enterprise_Controller_Core_Front_Action
{
    /**
     * Index action
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');

        if ($params = $this->getRequest()->getParam('params')) {
            $entity = Mage::getModel('enterprise_giftregistry/entity');
            $results = $entity->search($params);

            $this->getLayout()->getBlock('giftregistry.search.form')
                ->setSearchResults($results);
        }

        $this->renderLayout();
    }

    /**
     * Load type specific advanced search attributes
     */
    public function advancedAction()
    {
        $typeId = $this->getRequest()->getParam('type_id');
        $type = Mage::getModel('enterprise_giftregistry/type')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($typeId);

        Mage::register('current_giftregistry_type', $type);

        $this->loadLayout();
        $this->renderLayout();
    }
}