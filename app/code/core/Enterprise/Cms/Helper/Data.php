<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @category   Enterprise
 * @package    Enterprise_Cms
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Base helper
 *
 * @category   Enterprise
 * @package    Enterprise_Cms
 */

class Enterprise_Cms_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Array of available version numbers for user
     * @var array
     */
    protected $_versionsHash = null;

    /**
     * Array of admin users in system
     * @var array
     */
    protected $_usersHash = null;

    /**
     * Retrieve array of admin users in system
     *
     * @return array
     */
    public function getUsersArray()
    {
        if (!$this->_usersHash) {
            $collection = Mage::getModel('admin/user')->getCollection();
            $this->_usersHash = array();
            foreach ($collection as $user) {
                $this->_usersHash[$user->getId()] = $user->getUsername();
            }
        }

        return $this->_usersHash;
    }

    /**
     * Retrieve array of version available for current user and current page
     *
     * @param mixed $page
     * @return array
     */
    public function getVersionsArray($keyField, $valueField, $page)
    {
        if (!isset($this->_versionsHash[$keyField . $valueField])) {
            $userId = Mage::getSingleton('admin/session')->getUser()->getId();
            $collection = Mage::getModel('enterprise_cms/page_version')->getCollection()
                ->addPageFilter($page)
                ->addVisibilityFilter($userId,
                    Mage::getSingleton('enterprise_cms/config')->getAllowedAccessLevel());

            $this->_versionsHash[$keyField . $valueField] = $collection->getAsArray($keyField, $valueField);
        }

        return $this->_versionsHash[$keyField . $valueField];
    }

    /**
     * Prepare anchor's html code
     *
     * @param string $anchorText
     * @param array $attributes
     * @return string
     */
    public function prepareAnchorHtml($anchorText, $attributes = array())
    {
        $_preparedAttributes = array();
        foreach($attributes as $attribute => $value) {
            $_preparedAttributes[] = $attribute . '="' . $value . '"';
        }

        return '<a '.implode(' ', $_preparedAttributes).'>'. $this->htmlEscape($anchorText) . "</a>\n";
    }
}
