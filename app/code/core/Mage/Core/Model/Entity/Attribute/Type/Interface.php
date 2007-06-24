<?php
/**
 * Entity attribute type interface
 *
 * @package     Mage
 * @subpackage  Core
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
interface Mage_Core_Model_Entity_Attribute_Type_Interface
{
    public function getCode();
    public function getValueFieldName();
    public function saveValue();
    public function setConfig(Varien_Simplexml_Element $config);
    public function loadAttributesValues($entity);
}
