<?php

class Mage_Core_Service_Idl extends Varien_Object
{
    /** @var Magento_ObjectManager */
    protected $_objectManager;

    /**  @var Varien_Object */
    protected $_idl = null;

    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    public function getElement($path)
    {
        return $this->getIDL()->getData($path);
    }

    /**
     * @todo Complex types would be great to have in XML
     * @return array
     */
    public function getIDL()
    {
        if (null === $this->_idl) {
            // try from cache
            // if null run IDL parser/reader
            // ... and get it from cache ))

            // prototype
            $scheme = array(
                'catalogCategory' => array(
                    'class'   => 'Mage_Catalog_Service_Category',
                    'methods' => array(
                        'item' => array(
                            'args'           => array(
                                'entity_id' => array(),

                                'url_key'   => array(),

                                'store_id'  => array(
                                    'default' => null
                                ),

                                'version'   => array(
                                    'default' => null
                                ),

                                'fields'    => array()
                            ),
                            'id_field_alias' => 'category_id',
                            'return'         => array(
                                array('_resource' => 'catalogCategory')
                            )
                        )
                    )
                )
            );
            $this->_idl = new Varien_Object($scheme);
        }

        return $this->_idl;
    }
}
