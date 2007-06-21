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
        if( !$this->isDisabled(self::NOROUTE_PAGE_ID) ) {
            $this->setData( $this->getResource()->load(self::NOROUTE_PAGE_ID) );
        } else {
            return false;
        }
        return $this;
    }

    public function getResource()
    {
        return Mage::getResourceModel('cms/page');
    }

    public function save($page)
    {
        $this->getResource()->save($page);
    }

    public function enablePage($pageId)
    {
        $this->getResource()->enablePage($pageId);
        return $this;
    }

    public function disablePage($pageId)
    {
        $this->getResource()->disablePage($pageId);
        return $this;
    }
}