<?php
class Mage_SearchLucene_Block_Results extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getResults(Zend_Controller_Request_Http $request)
    {
        $this->setTemplate('searchlucene/result.phtml');
        $query = $request->getParam('q', false);
        $queryEscaped = htmlspecialchars($query);

        Mage::registry('action')->getLayout()->getBlock('head.meta')->setTitle('Search results for: '.$queryEscaped);

        $var = Mage::getBaseDir('var');
        $index_dir = $var . DS . 'search' . DS . 'index';
        $index = Zend_Search_Lucene::open($index_dir);
        $hits = $index->find($query);

        $this->assign('hits', $hits);
        $this->assign('query', $queryEscaped);
    }
}