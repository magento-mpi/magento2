<?php
/**
 * Log visitor aggregator
 *
 * @package     Mage
 * @subpackage  Log
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Log_Model_Visitor_Aggregator extends Varien_Object
{
    public function getResource()
    {
        return Mage::getResourceModel('log/visitor_aggregator');
    }

    public function update()
    {
        $this->getResource()->update();
        return $this;
    }
}