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
            $scheme = array(
                'catalogProduct'  => array(
                    'class'  => 'Mage_Catalog_Service_Product',
                    'fields'         => array(
                        'product_id' => array(
                            'label'      => 'Entity ID',
                            'type'       => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                            'input_type' => 'label',
                            'size'       => null,
                            'identity'   => true,
                            'nullable'   => false,
                            'primary'    => true,
                        )
                    ),
                    'global_params'  => array(
                        'store_id'         => array(
                            'default' => null
                        ),
                        'attribute_set_id' => array(
                            'default' => null
                        ),
                        'type_id'          => array(
                            'default' => null
                        )
                    ),
                    'id_field_alias' => 'id'
                ),
                'catalogCategory' => array(
                    'class'  => 'Mage_Catalog_Service_Category',
                    'fields'         => array(
                        'entity_id' => array(
                            'label'      => 'Entity ID',
                            'type'       => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                            'input_type' => 'label',
                            'size'       => null,
                            'identity'   => true,
                            'nullable'   => false,
                            'primary'    => true,
                        )
                    ),
                    'global_params'  => array(
                        'store_id' => array(
                            'default' => null
                        )
                    ),
                    'id_field_alias' => 'id'
                )
            );
            $this->_idl = new Varien_Object($scheme);
        }

        return $this->_idl;
    }
}
