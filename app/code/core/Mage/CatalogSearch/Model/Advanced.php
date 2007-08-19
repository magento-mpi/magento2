<?php
/**
 * Catalog advanced search model
 *
 * @package     Mage
 * @subpackage  Catalogsearch
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_CatalogSearch_Model_Advanced extends Varien_Object
{
    public function getAttributes()
    {
        $attributes = $this->getData('attributes');
        if (is_null($attributes)) {
            $attributes = '';
            $this->setData('attributes', $attributes);
        }
        return $attributes;
    }
}
