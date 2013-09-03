<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Filter
 * @copyright  {copyright}
 * @license    {license_link}
 */


namespace Magento\Filter\Template;

class Simple extends \Magento\Object implements \Zend_Filter_Interface
{
	protected $_startTag = '{{';
	protected $_endTag = '}}';

	public function setTags($start, $end)
	{
		$this->_startTag = $start;
		$this->_endTag = $end;
		return $this;
	}

	public function filter($value)
	{
		return preg_replace('#'.$this->_startTag.'(.*?)'.$this->_endTag.'#e', '$this->getData("$1")', $value);
	}
}
