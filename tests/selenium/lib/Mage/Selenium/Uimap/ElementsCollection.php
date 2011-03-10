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
 * UIMap Atomic Elements collection class
 *
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Selenium_Uimap_ElementsCollection extends ArrayObject
{
    /**
     * Type of element
     * @var string
     */
    protected $_type = '';

    /**
     * Constructor
     * @param string Type of element
     * @param array Elements array
     */
    public function __construct($type, $objects)
    {
        $this->_type = $type;
        parent::__construct($objects);
    }

    /**
     * Get type of element
     * @return string
     */
    public function getType()
    {
        return $_type;
    }

    /**
     * Get eement by Id
     * @param string Id of element
     * @return string
     */
    public function get($id)
    {
        return isset($this[$id])?$this[$id]:null;
    }
    
}
