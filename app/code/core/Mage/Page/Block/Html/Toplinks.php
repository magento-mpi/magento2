<?php
/**
 * Html page block
 *
 * @package     Mage
 * @subpackage  Page
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Sergiy Lysak <sergey@varien.com>
 */
class Mage_Page_Block_Html_Toplinks extends Mage_Core_Block_Template
{
    /**
     * Array of toplinks
     *
     * array(
     *  [$index] => array(
     *                  ['liParams']
     *                  ['aParams']
     *                  ['innerText']
     *                  ['beforeText']
     *                  ['afterText']
     *                  ['first']
     *                  ['last']
     *              )
     * )
     *
     * @var array
     */
    protected $_toplinks = array();

    function __construct()
    {
    	parent::__construct();
    	$this->setTemplate('page/html/top.links.phtml');
    }

    function addLink($liParams, $aParams, $innerText, $beforeText='', $afterText='')
    {
        $params = '';
        if (!empty($liParams) && is_array($liParams)) {
            foreach ($liParams as $key=>$value) {
                $params .= ' ' . $key . '="' . addslashes($value) . '"';
            }
        } elseif (is_string($liParams)) {
            $params .= ' ' . $liParams;
        }
        $toplinkInfo['liParams'] = $params;
        $params = '';
        if (!empty($aParams) && is_array($aParams)) {
            foreach ($aParams as $key=>$value) {
                $params .= ' ' . $key . '="' . addslashes($value) . '"';
            }
        } elseif (is_string($aParams)) {
            $params .= ' ' . $aParams;
        }
        $toplinkInfo['aParams'] = $params;
        $toplinkInfo['innerText'] = $innerText;
        $toplinkInfo['beforeText'] = $beforeText;
        $toplinkInfo['afterText'] = $afterText;
        $this->_prepareArray($toplinkInfo, array('liParams', 'aParams', 'innerText', 'beforeText', 'afterText', 'first', 'last'));
    	$this->_toplinks[] = $toplinkInfo;
    }

    function toHtml()
    {
        if (is_array($this->_toplinks)) {
            reset($this->_toplinks);
            $this->_toplinks[key($this->_toplinks)]['first'] = true;
            end($this->_toplinks);
            $this->_toplinks[key($this->_toplinks)]['last'] = true;
        }
    	$this->assign('toplinks', $this->_toplinks);
    	return parent::toHtml();
    }
}
