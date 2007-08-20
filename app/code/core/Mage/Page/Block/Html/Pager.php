<?php
/**
 * Html page block
 *
 * @package     Mage
 * @subpackage  Page
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 * @author      Sergiy Lysak <sergey@varien.com>
 *
 * @todo        separate order, mode and pager
 */
class Mage_Page_Block_Html_Pager extends Mage_Core_Block_Template
{
    protected $_collection = null;
    protected $_pageVarName     = 'p';
    protected $_limitVarName    = 'limit';
    protected $_availableLimit  = array(10,20,50);
    protected $_dispersion      = 3;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('page/html/pager.phtml');
    }
    
    public function getCurrentPage()
    {
        if ($page = (int) $this->getRequest()->getParam($this->getPageVarName())) {
            return $page;
        }
        return 1;
    }
    
    public function getLimit()
    {
        if ($limit = $this->getRequest()->getParam($this->getLimitVarName())) {
            if (in_array($limit, $this->_availableLimit)) {
                return $limit;
            }
        }
        
        return $this->_availableLimit[0];
    }
    
    public function setCollection($collection)
    {
        $this->_collection = $collection
            ->setCurPage($this->getCurrentPage())
            ->setPageSize($this->getLimit());
            
        return $this;
    }
    
    public function getCollection()
    {
        return $this->_collection;
    }

    public function setPageVarName($varName)
    {
        $this->_pageVarName = $varName;
        return $this;
    }
    
    public function getPageVarName()
    {
        return $this->_pageVarName;
    }

    public function setLimitVarName($varName)
    {
        $this->_limitVarName = $varName;
        return $this;
    }
    
    public function getLimitVarName()
    {
        return $this->_limitVarName;
    }
    
    public function setAvailableLimit(array $limits)
    {
        $this->_availableLimit = $limits;
    }
    
    public function getAvailableLimit()
    {
        return $this->_availableLimit;
    }
    
    public function getFirstNum()
    {
        $collection = $this->getCollection();
        return $collection->getPageSize()*($collection->getCurPage()-1)+1;
    }

    public function getLastNum()
    {
        $collection = $this->getCollection();
        return $collection->getPageSize()*($collection->getCurPage()-1)+$collection->count();
    }
    
    public function getTotalNum()
    {
        return $this->getCollection()->getSize();
    }
    
    public function isFirstPage()
    {
        return $this->getCollection()->getCurPage() == 1;
    }
    
    public function isLastPage()
    {
        return $this->getCollection()->getCurPage() >= $this->getCollection()->getLastPageNumber();
    }
    
    public function isLimitCurrent($limit)
    {
        return $limit == $this->getLimit();
    }
    
    public function isPageCurrent($page)
    {
        return $page == $this->getCurrentPage();
    }
    
    public function getPages()
    {
        $pages = array();
        for ($i=$this->getCollection()->getCurPage(-$this->_dispersion); $i <= $this->getCollection()->getCurPage(+$this->_dispersion); $i++)
        {
            $pages[] = $i;
        }
        
        return $pages;
    }
    
    public function getFirstPageUrl()
    {
        return $this->getPageUrl(1);
    }

    public function getPreviousPageUrl()
    {
        return $this->getPageUrl($this->getCollection()->getCurPage(-1));
    }

    public function getNextPageUrl()
    {
        return $this->getPageUrl($this->getCollection()->getCurPage(+1));
    }

    public function getLastPageUrl()
    {
        return $this->getPageUrl($this->getCollection()->getLastPageNumber());
    }

    public function getPageUrl($page)
    {
        return $this->getPagerUrl(array($this->getPageVarName()=>$page));
    }

    public function getLimitUrl($limit)
    {
        return $this->getPagerUrl(array($this->getLimitVarName()=>$limit));
    }
    
    public function getPagerUrl($params=array())
    {
        $params['_current'] = true;
        return $this->getUrl('*/*/*', $params);
    }
}

