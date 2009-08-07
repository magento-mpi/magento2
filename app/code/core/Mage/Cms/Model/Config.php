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
 * @category   Mage
 * @package    Mage_Cms
 * @copyright  Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * CMS Config model
 *
 * @category   Mage
 * @package    Mage_Cms
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Cms_Model_Config
{
    const XML_PATH_CMS_PAGE_STATUSES = 'adminhtml/cms/page/status';

    protected $_pageStatuses;

    /**
     * Cms widgets installed in system.
     *
     * @var Varien_Simplexml_Element
     */
    protected $_widgets;

    /**
     * Retrieve page statuses from config
     *
     * @return array
     */
    public function getPageStatuses()
    {
        if (is_null($this->_pageStatuses)) {
            $statusNode = Mage::getConfig()
                ->getNode(self::XML_PATH_CMS_PAGE_STATUSES);
            $this->_pageStatuses = array();

            if ($statusNode) {
                foreach ($statusNode->children() as $status) {
                    $this->_pageStatuses[(string)$status->value] = Mage::helper('cms')->__((string)$status->label);
                }
            }
        }

        return $this->_pageStatuses;
    }

    /**
     * Retrieve widget by code
     *
     * @param string $code
     * @return Varien_Simplexml_Element
     */
    public function getWidget($code)
    {
        if ($code) {
            return $this->getWidgets()->$code;
        }

        return false;
    }

    /**
     * Retrieve widgets declared in system.
     *
     * @return Varien_Simplexml_Element
     */
    public function getWidgets()
    {
        if (!$this->_widgets) {
            $config = Mage::getConfig()->loadModulesConfiguration('widget.xml');
            $this->_widgets = $config->getNode('widgets');
        }

        return $this->_widgets;
    }
}
