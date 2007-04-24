<?php
class Mage_Core_SearchController extends Mage_Core_Controller_Admin_Action
{
    function doAction()
    {
        $searchModules = Mage::getConfig()->getXml("admin/globalSearch");
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
