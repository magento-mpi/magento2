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
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Simple links list block
 *
 * @author     Michael Bessolov <michael@varien.com>
 */
class Mage_Core_Block_Template_Links extends Mage_Core_Block_Template
{

    protected $_links = array();

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('page/html/links.phtml');
    }

    public function getLinks()
    {
        return $this->_links;
    }

    /**
     * Add link to the list
     *
     * @param string $label
     * @param string $url
     * @param string $title
     * @param boolean $prepare
     * @param array $urlParams
     * @param int $position
     * @param string|array $liParams
     * @param string|array $aParams
     * @param string $beforeText
     * @param string $afterText
     * @return Mage_Page_Block_Html_Toplinks
     */
    public function addLink($label, $url='', $title='', $prepare=false, $urlParams=array(),
        $position=null, $liParams=null, $aParams=null, $beforeText='', $afterText='')
    {
        $link = new Varien_Object(array(
            'label'         => $label,
            'url'           => ($prepare ? $this->getUrl($url, (is_array($urlParams) ? $urlParams : array())) : $url),
            'title'         => $title,
            'li_params'     => $this->_prepareParams($liParams),
            'a_params'      => $this->_prepareParams($aParams),
            'before_text'   => $beforeText,
            'after_text'    => $afterText,
        ));

        if (is_int($position)) {
            $this->_links = array_splice($this->_links, $position, 0, array($link));
        } else {
            $this->_links[] = $link;
        }

        return $this;
    }

    /**
     * Prepare tag attributes
     *
     * @param string|array $params
     * @return string
     */
    protected function _prepareParams($params)
    {
        if (is_string($params)) {
            return $params;
        } elseif (is_array($params)) {
            $result = '';
            foreach ($params as $key=>$value) {
                $result .= ' ' . $key . '="' . addslashes($value) . '"';
            }
            return $result;
        }
        return '';
    }

    /**
     * Set first/last
     *
     * @return Mage_Page_Block_Html_Toplinks
     */
    protected function _beforeToHtml()
    {
        if (!empty($this->_links)) {
            reset($this->_links);
            $this->_links[key($this->_links)]->setIsFirst(true);
            end($this->_links);
            $this->_links[key($this->_links)]->setIsLast(true);
        }
        return parent::_beforeToHtml();
    }

}
