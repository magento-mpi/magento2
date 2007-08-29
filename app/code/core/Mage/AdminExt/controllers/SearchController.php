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

class Mage_Admin_SearchController extends Mage_Core_Controller_Front_Action
{
    function doAction()
    {
        $searchModules = Mage::getConfig()->getNode("admin/search/global/collections");
        $items = array();
        if (empty($searchModules)) {
            $items[] = array('id'=>'error', 'type'=>'Error', 'name'=>'No search modules registered', 'description'=>'Please make sure that all global admin search modules are installed and activated.');
            $totalCount = 1;
        } else {
            $request = $this->getRequest()->getPost();
            foreach ($searchModules->children() as $searchConfig) {
                $className = $searchConfig->getClassName();
                $searchInstance = new $className();
                $results = $searchInstance->setStart($request['start'])->setLimit($request['limit'])->setQuery($request['query'])->load()->getResults();
                $items = array_merge_recursive($items, $results);
            }
            $totalCount = sizeof($items);
        }

        $data = array('totalCount'=>$totalCount, 'items'=>$items);
        $json = Zend_Json::encode($data);
        
        $this->getResponse()->setBody($json);
    }
}
