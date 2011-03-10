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
 * Page uimap class
 *
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Selenium_Uimap_Page extends Mage_Selenium_Uimap_Abstract
{
    protected $pageId = '';
    protected $mca = '';
    protected $title = '';

    public function  __construct($pageId, array &$pageContainer) {
        $this->pageId = $pageId;
        if(isset($pageContainer['mca'])) $this->mca = $pageContainer['mca'];
        if(isset($pageContainer['title'])) $this->title = $pageContainer['title'];
        if(isset($pageContainer['uimap'])) $this->parseContainerArray($pageContainer['uimap']);
    }

    /**
     * Get page ID
     * @return string
     */
    public function getPageId()
    {
        return $this->pageId;
    }

    /**
     * Get page mca
     * @return string
     */
    public function getMca()
    {
        return $this->mca;
    }

    /**
     * Get page title
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get the main form defined on the current page
     *
     * @return Mage_Selenium_Uimap_Form
     */
    public function getMainForm()
    {
        return $this->_elements['form']; // Stub
    }

    public function getMessage($id)
    {
        return isset($this->_elements['messages'][$id])?$this->_elements['messages'][$id]:'';
    }

    /**
     * Get all buttons defined on the current form, as well as on its tabs and fieldsets
     *
     * @return array
     */
    public function getAllButtons()
    {
        if(empty($this->_elements_cache['buttons'])) {
            $cache = array();
            $this->getElementsRecursive('buttons', $this->_elements, $cache);

            $this->_elements_cache['buttons'] = new Mage_Selenium_Uimap_ElementsCollection('buttons',
                    $this->getElementsRecursive('buttons', $this->_elements, $cache));
        }
        return $this->_elements_cache['buttons'];
    }

}
