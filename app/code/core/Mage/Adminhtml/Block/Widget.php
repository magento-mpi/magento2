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
    /**
     * Request object
     *
     * @var Mage_Core_Controller_Zend_Request
     */
    protected $_request;
    
    public function __construct() 
    {
        parent::__construct();
        if (Mage::registry('controller')) {
            $this->_request = Mage::registry('controller')->getRequest();
        }
        else {
            throw new Exception('Can\'t retrieve request object');
        }
    }
    
    public function getId()
    {
        if ($this->getData('id')===null) {
            $this->setData('id', '_'.md5(time()));
        }
        return $this->getData('id');
    }
    
    public function getCurrentUrl($params=array())
    {
        $urlParams = $this->_request->getParams();
        foreach ($params as $paramCode=>$paramValue) {
        	$urlParams[$paramCode] = $paramValue;
        }

        return Mage::getUrl('adminhtml', $urlParams);
    }
}
