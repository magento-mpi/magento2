<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Core\Model\Config\Scope;

use Magento\App\Config\Scope\ReaderInterface;
use Magento\App\Config\Scope\ReaderPoolInterface;

class ReaderPool implements ReaderPoolInterface
{
    /**
     * List of readers
     *
     * @var array
     */
    protected $_readers = array();

    /**
     * @param ReaderInterface $default
     * @param ReaderInterface $website
     * @param ReaderInterface $store
     */
    public function __construct(ReaderInterface $default, ReaderInterface $website, ReaderInterface $store)
    {
        $this->_readers = array(
            'default' => $default,
            'website' => $website,
            'websites' => $website,
            'store' => $store,
            'stores' => $store
        );
    }

    /**
     * Retrieve reader by scope type
     *
     * @param string $scopeType
     * @return mixed
     */
    public function getReader($scopeType)
    {
        return $this->_readers[$scopeType];
    }
}
