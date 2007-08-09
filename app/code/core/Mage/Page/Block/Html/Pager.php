<?php
/**
 * Html page block
 *
 * @package     Mage
 * @subpackage  Page
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Sergiy Lysak <sergey@varien.com>
 *
 * @todo        separate order, mode and pager
 */
class Mage_Page_Block_Html_Pager extends Mage_Core_Block_Template
{
    protected $_collection = null;
    protected $_urlPrefix = null;
    protected $_viewBy = null;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('page/html/pager.phtml');
    }

    public function setCollection($collection)
    {
        $this->_collection = $collection;
        return $this;
    }

    public function getCollection()
    {
        return $this->_collection;
    }

    public function setParam($key, $value)
    {
        $this->assign($key, $value);
        return $this;
    }

    public function setUrlPrefix($prefix)
    {
        $this->_urlPrefix = $prefix;
        return $this;
    }

    public function getUrlPrefix()
    {
        return $this->_urlPrefix;
    }

    public function getFirstItemNum()
    {
        return $this->getCollection()->getPageSize()*($this->getCollection()->getCurPage()-1)+1;
    }

    public function getLastItemNum()
    {
        return $this->getCollection()->getPageSize()*($this->getCollection()->getCurPage()-1)+$this->getCollection()->count();
        //return $this->getCollection()->getSize();
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
        return $this->getFeaturedPagerUrl(array('p'=>$page));
    }

    public function getFeaturedPagerUrl($params=array())
    {
        $request = clone $this->getRequest();
        foreach($params as $key=>$val) {
            $request->setParam($key, $val)->getParams();
        }
        return $this->getUrl($this->getUrlPrefix() . '/*/*', $request->getParams());
    }

    public function setViewBy($key, $values=array())
    {
        $this->_viewBy[$key] = $values;
        return $this;
    }

    public function getViewBy($key='')
    {
        if(is_array($this->_viewBy)) {
            if($key != '') {
                return $this->_viewBy[$key];
            }
            else {
                return $this->_viewBy;
            }
        }
        return false;
    }

    public function getIsViewBy($key, $value='')
    {
        if($value == '' && isset($this->_viewBy[$key])) {
            return true;
        }
        elseif($value != '' && in_array($value, $this->_viewBy[$key])) {
            return true;
        }
        return false;
    }

    protected function _beforeToHtml()
    {
        $request = $this->getRequest();
        $this->getCollection()
//            ->setOrder($request->getParam('order', 'position'), $request->getParam('dir', 'asc'))
            ->setCurPage($request->getParam('p', 1))
            ->setPageSize($request->getParam('limit', 10))
            ->load();
        return parent::_beforeToHtml();
    }
}

