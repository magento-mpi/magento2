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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    tests
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Abstract uimap class
 *
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Selenium_Uimap_Abstract
{
    protected $xPath = '';
    protected $_elements = array();
    protected $_elements_cache = array();

    /**
     * Retrieve xpath of the current element
     *
     * @return string|null
     */
    public function getXpath()
    {
        return $xPath;
    }

    public function &getElements()
    {
        return $this->_elements;
    }

    protected function parseContainerArray(array &$container)
    {
        foreach($container as $formElemKey=>&$formElemValue) {
            $newElement = Mage_Selenium_Uimap_Factory::createUimapElement($formElemKey, $formElemValue);
            if(!empty($newElement)) {
                if(!isset($this->_elements[$formElemKey])) {
                    $this->_elements[$formElemKey] = $newElement;
                } else {
                    if($this->_elements[$formElemKey] instanceof ArrayObject) {
                        $this->_elements[$formElemKey].append($newElement);
                    } else {
                        //var_dump($formElemKey);
                        //var_dump($formElemValue);
                        // @TODO Some reaction?
                        //die;
                    }
                }
            }
        }
    }

    protected function getElementsRecursive($elementType, &$container, &$cache)
    {
        foreach($container as $elKey=>&$elValue) {
            if($elValue instanceof ArrayObject) {
                if($elKey==$elementType && $elValue instanceof Mage_Selenium_Uimap_ElementsCollection) {
                    $cache = array_merge($cache, $elValue->getArrayCopy());
                } else {
                    $this->getElementsRecursive($elementType, $elValue, $cache);
                }
            } elseif($elValue instanceof Mage_Selenium_Uimap_Abstract) {
                $this->getElementsRecursive($elementType, $elValue->getElements(), $cache);
            }
        }

        return $cache;
    }

    public function getAllElements($elementType)
    {
        if(empty($this->_elements_cache[$elementType])) {
            $cache = array();
            $this->_elements_cache[$elementType] = new Mage_Selenium_Uimap_ElementsCollection($elementType,
                    $this->getElementsRecursive($elementType, $this->_elements, $cache));
        }

        return $this->_elements_cache[$elementType];
    }

    public function __call($name,  $arguments) {
        if(preg_match('|^getAll(\w+)$|', $name)) {
            $elementName = strtolower(substr($name, 6));
            if(!empty($elementName)) {
                return $this->getAllElements($elementName);
            }
        }elseif(preg_match('|^get(\w+)$|', $name)) {
            $elementName = strtolower(substr($name, 3));
            if(!empty($elementName) && isset($this->_elements[$elementName])) {
                return $this->_elements[$elementName];
            }
        }

        return null;
    }

}
