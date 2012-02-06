<?php

class Mage_Api2_Model_Acl_Filter
{
    const OPERATION_READ  = 'read';
    const OPERATION_WRITE = 'write';

    /**
     * Resource type
     *
     * @var string
     */
    protected $_resourceType;

    /**
     * User type
     *
     * @var string
     */
    protected $_userType;

    /**
     * Attributes to include
     *
     * @var array
     */
    protected $_include;

    public function in(array $data, $resourceType = null, $userType = null)
    {
        $operation = self::OPERATION_WRITE;
        $resourceType = $resourceType === null ? $this->getResourceType() : $resourceType;
        $userType = $userType === null ? $this->getUserType() : $userType;

        $allowed = $this->getAllowedAttributes($resourceType, $operation, $userType);

        $data = $this->filter($allowed, $data);

        return $data;
    }

    public function out(array $data, $resourceType = null, $userType = null)
    {
        $operation = self::OPERATION_READ;
        $resourceType = $resourceType === null ? $this->getResourceType() : $resourceType;
        $userType = $userType === null ? $this->getUserType() : $userType;

        //TODO how we process &include, what attributes we show by default, all allowed, all static?
        $allowed = $this->getAllowedAttributes($resourceType, $operation, $userType);

        $data = $this->filter($allowed, $data);

        return $data;
    }

    public function getAttributesToInclude($resourceType = null, $userType = null)
    {
        $operation = self::OPERATION_READ;
        $resourceType = $resourceType === null ? $this->getResourceType() : $resourceType;
        $userType = $userType === null ? $this->getUserType() : $userType;

        $attributes = $this->getAllowedAttributes($resourceType, $operation, $userType);

        $include = $this->getInclude();

        if (in_array('*', $include)) {
            return $attributes;
        }

        return $this->filter2($include, $attributes);
    }

    /**
     * Strip attributes out of collection items
     *
     * @param array $items
     * @param null $resourceType
     * @param null $userType
     * @return mixed
     */
    public function collectionOut($items, $resourceType = null, $userType = null)
    {
        $attributes = $this->getAttributesToInclude($resourceType, $userType);
        foreach ($items as &$data) {
            $data = $this->filter($attributes, $data);
        }

        return $items;
    }

    /**
     * Return only the data which is allowed
     *
     * @param array $allowed numeric
     * @param array $data associative
     * @return array
     */
    protected function filter(array $allowed, array $data)
    {
        //TODO use whatever is faster

        /*$filtered = array();
        foreach ($allowed as $attribute) {
            if (isset($data[$attribute])) {
                $filtered[$attribute] = $data[$attribute];
            }
        }*/

        foreach ($data as $attribute=>$value) {
            if (!in_array($attribute, $allowed)) {
                unset($data[$attribute]);
            }
        }

        return $data;
    }

    /**
     * Return only the data which is asked to include
     *
     * @param array $include numeric
     * @param array $attributes numeric
     * @return array
     */
    protected function filter2(array $include, array $attributes)
    {
        return array_intersect_key($include, $attributes);
    }

    /**
     * Fetch array of allowed attributes for given resource type, operation and user type.
     *
     * @param string $resourceType
     * @param string $operation
     * @param string $userType
     * @return array
     */
    protected function getAllowedAttributes($resourceType, $operation, $userType)
    {
        //TODO backend to get real attributes allowed

        $example = array(
            'product'   => array(
                self::OPERATION_READ    => array('id', 'entity_id', 'name', 'title', 'sku'),
                self::OPERATION_WRITE   => array('name'),
            ),
            'products'   => array(
                self::OPERATION_READ    => array('id', 'entity_id', 'name', 'title', 'sku', 'status',),
                self::OPERATION_WRITE   => array('type', 'set', 'sku'),
            ),
            'orders' => array(
                self::OPERATION_READ => array('entity_id', 'customer_id', 'state', 'subtotal', 'created_at')
            ),
            'review' => array(
                self::OPERATION_READ  => array('review_id', 'product_id', 'status_id', 'stores', 'nickname', 'title',
                    'detail'),
                self::OPERATION_WRITE => array('status_id', 'stores', 'nickname', 'title', 'detail')
            ),
            'reviews' => array(
                self::OPERATION_READ  => array('review_id', 'product_id', 'status_id', 'stores', 'nickname', 'title',
                    'detail'),
                self::OPERATION_WRITE => array('product_id', 'status_id', 'stores', 'nickname', 'title', 'detail')
            )
        );

        $attributes = $example[$resourceType][$operation];

        return $attributes;
    }

    /**
     * Get resource type
     * If not exists error
     *
     * @throws Exception
     * @return string
     */
    public function getResourceType()
    {
        if (!$this->_resourceType) {
            throw new Exception('Resource type is not set.');
        }

        return $this->_resourceType;
    }

    /**
     * Set resource type
     * @param string $resourceType
     * @return Mage_Api2_Model_Acl_Filter
     */
    public function setResourceType($resourceType)
    {
        $this->_resourceType = $resourceType;

        return $this;
    }

    /**
     * Get user type
     * If not exists error
     *
     * @throws Exception
     * @return string
     */
    public function getUserType()
    {
        if (!$this->_userType) {
            throw new Exception('User type is not set.');
        }

        return $this->_userType;
    }

    /**
     * Set user type
     *
     * @param string $userType
     * @return Mage_Api2_Model_Acl_Filter
     */
    public function setUserType($userType)
    {
        $this->_userType = $userType;

        return $this;
    }

    /**
     * Get attributes to include
     *
     * @return array
     */
    public function getInclude()
    {
        if (!is_array($this->_include)) {
            throw new Exception('Invalid or not set attributes to include.');
        }
        return $this->_include;
    }

    /**
     * Set attributes to include
     *
     * @param array $include
     */
    public function setInclude(array $include)
    {
        $this->_include = $include;
    }
}
