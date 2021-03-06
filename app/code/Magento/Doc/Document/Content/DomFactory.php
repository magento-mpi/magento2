<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Doc\Document\Content;

use Magento\Doc\Document\DomInterface;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class DomFactory
 * @package Magento\Doc\Document\Content
 */
class DomFactory
{
    /**
     * Object Manager instance
     *
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var string
     */
    protected $domDocumentClass;

    /**
     * Constructor
     *
     * @param ObjectManagerInterface $objectManager
     * @param string $domDocumentClass
     */
    public function __construct(ObjectManagerInterface $objectManager, $domDocumentClass = 'Magento\Doc\Document\Content\Dom')
    {
        $this->objectManager = $objectManager;
        $this->domDocumentClass = $domDocumentClass;
    }

    /**
     * Create Dom object instance
     *
     * @param array $arguments
     * @return DomInterface
     */
    public function create(array $arguments = [])
    {
        return $this->objectManager->create($this->domDocumentClass, $arguments);
    }
}
