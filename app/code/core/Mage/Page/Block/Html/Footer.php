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
class Mage_Page_Block_Html_Footer extends Mage_Core_Block_Template
{
    public function setCopyright($copyright)
    {
        $this->_copyright = $copyright;
        return $this;
    }
    
    public function getCopyright()
    {
        if (!$this->_copyright) {
            $this->_copyright = $this->getDesignConfig('page/footer/copyright');
        }
            
        return $this->_copyright;
    }
}
