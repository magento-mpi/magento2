<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Doc\Document\Outline;

use Magento\Framework\ObjectManager;
use Magento\Doc\Document\DomInterface;

/**
 * Class DomFactory
 * @package Magento\Doc\Document\Outline
 */
class DomFactory
{
    /**
     * Object Manager instance
     *
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var string
     */
    protected $domDocumentClass;

    /**
     * Constructor
     *
     * @param ObjectManager $objectManager
     * @param string $domDocumentClass
     */
    public function __construct(ObjectManager $objectManager, $domDocumentClass = 'Magento\Doc\Document\Outline\Dom')
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
