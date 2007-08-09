<?php
/**
 * Customer account navigation sidebar
 *
 * @package     Mage
 * @subpackage  Customer
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Customer_Block_Account_Navigation extends Mage_Core_Block_Template
{

    protected $_links = array();

    protected $_activeLink = false;

    public function addLink($name, $path, $label, $base=null, $after=null)
    {
        $this->_links[$name] = new Varien_Object(array(
            'name' => $name,
            'path' => $path,
            'label' => $label,
            'url' => (is_null($base) ? Mage::getBaseUrl() : $base) . $path,
        ));
        return $this;
    }

    public function setActive($path)
    {
        $this->_activeLink = rtrim($path, '/');
    }

    public function getLinks()
    {
        if (empty($this->_activeLink)) {
            if ('index' == $this->getRequest()->getActionName()) {
                if ('index' == $this->getRequest()->getControllerName()) {
                    $this->_activeLink = $this->getRequest()->getModuleName();
                } else {
                    $this->_activeLink = $this->getRequest()->getModuleName() . '/' . $this->getRequest()->getControllerName();
                }
            } else {
                $this->_activeLink = $this->getAction()->getFullActionName('/');
            }
        }
        return $this->_links;
    }

    public function isActive($link)
    {
        if ($this->_activeLink && (rtrim($link->getPath(), '/') == $this->_activeLink)) {
            return true;
        }
        return false;
    }

}
