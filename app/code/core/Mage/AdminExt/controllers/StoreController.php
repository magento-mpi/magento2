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
 * @category   Mage
 * @package    Mage_AdminExt
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Admin_StoreController extends Mage_Core_Controller_Admin_Action
{
    function listAction()
    {
        $data = array();
        //TODO: move node to JS
        $data[] = array(
            'value' => 0,
            'text'  => __('All Stores')
        );
        $arrSites = Mage::getResourceModel('core/store_collection')->load();
        
        foreach ($arrSites as $store) {
            $data[] = array(
                'value' => $store->getStoreId(),
                'text'  => $store->getStoreCode()
            );
        }
        
        $this->getResponse()->setBody(Zend_Json::encode($data));
    }
    
    function treeListAction() {
        $data = array(
               array(
                    'id' => 1,
                    'text'  => 'store1'
                ),
                array(
                    'id' => 2,
                    'text'  => 'store2'
                ),
                array(
                    'id' => 3,
                    'text'  => 'store3'
                ),
                array(
                    'id' => 4,
                    'text'  => 'store4'
                )
        );
        $this->getResponse()->setBody(Zend_Json::encode($data));
    }
}