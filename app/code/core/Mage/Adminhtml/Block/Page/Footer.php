<?php
/**
 * Adminhtml footer block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Page_Footer extends Mage_Core_Block_Template 
{
    public function __construct() 
    {
        $this->setTemplate('page/footer.phtml');
        $this->setShowProfiler(true);
    }
    
    public function getProfiler()
    {
        return $this->getLayout()->createBlock('core/profiler')->toHtml();
    }
    
    public function getProfilerTimers()
    {
        return Varien_Profiler::getTimers();
    }
    
    public function formatTime($time)
    {
        $time = number_format($time, 5);
        if ($time>0.01) {
            $time = ''.$time.'';
        }
        return $time;
    }
}
