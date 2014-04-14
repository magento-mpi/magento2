<?php
/**
 * Root ACL Resource
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Acl;

class RootResource
{
    /**
     * Root resource id
     *
     * @var string
     */
    protected $_identifier;

    /**
     * @param string $identifier
     */
    public function __construct($identifier)
    {
        $this->_identifier = $identifier;
    }

    /**
     * Retrieve root resource id
     *
     * @return string
     */
    public function getId()
    {
        return $this->_identifier;
    }
}
