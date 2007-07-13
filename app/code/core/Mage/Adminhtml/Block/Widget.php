<?php
/**
 * Base widget class
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Widget extends Mage_Core_Block_Template 
{
    public function getId()
    {
        if ($this->getData('id')===null) {
            $this->setData('id', 'id_'.md5(time()));
        }
        return $this->getData('id');
    }
    
    public function getHtmlId()
    {
        return $this->getId();
    }
    
    public function getCurrentUrl($params=array())
    {
        $urlParams = $this->getRequest()->getParams();
        foreach ($params as $paramCode=>$paramValue) {
        	$urlParams[$paramCode] = $paramValue;
        }

        return Mage::getUrl('*/*/*', $urlParams);
    }

    protected function _addBreadcrumb($label, $title=null, $link=null)
    {
        $this->getLayout()->getBlock('breadcrumbs')->addLink($label, $title, $link);
    }
}
