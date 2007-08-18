<?php
/**
 * Cms page content
 *
 * @package     Mage
 * @subpackage  Cms
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Moshe Gurvich <moshe@varien.com>
 */
class Mage_Cms_Block_Page extends Mage_Core_Block_Abstract
{
    public function toHtml()
    {
        $processor = Mage::getModel('core/email_template_filter');
        
        return $processor->filter($this->getPage()->getContent());
    }
}
