<?php
/**
 * Cms block content
 *
 * @package     Mage
 * @subpackage  Cms
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Cms_Block_Block extends Mage_Core_Block_Abstract
{
    public function toHtml()
    {
		if (!$this->_beforeToHtml()) {
			return '';
		}
        $html = '';
        if ($block = $this->getBlockId()) {
            $content = Mage::getModel('cms/block')
                ->load($block)
                ->getContent();

            $processor = Mage::getModel('core/email_template_filter');
            $html = $processor->filter($content);
        }
        return $html;
    }
}
