<?php
/**
 * CMS page model
 *
 * @package     Mage
 * @subpackage  Cms
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Cms_Model_Page extends Varien_Object
{

    const NOROUTE_PAGE_ID = 'no-route';

    public function load($pageId=null)
    {
        if( is_null($pageId) ) {
            return $this->noRoutePage();
        }

        $this->setData( $this->getResource()->load($pageId) );
        return $this;
    }

    public function noRoutePage()
    {
        $this->setData( $this->getResource()->load(self::NOROUTE_PAGE_ID) );
        return $this;
    }

    public function getResource()
    {
        return Mage::getModel('cms_resource/page');
    }
}