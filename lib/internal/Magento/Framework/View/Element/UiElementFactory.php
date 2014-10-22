<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Element;

use Magento\Framework\ObjectManager;
use Magento\Ui\Component\Form\Element\ElementInterface;
use Magento\Framework\View\Element\UiComponent\Context as UiContext;

/**
 * Class UiElementFactory
 */
class UiElementFactory
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var UiContext
     */
    protected $context;

    /**
     * Constructor
     *
     * @param ObjectManager $objectManager
     * @param UiContext $context
     */
    public function __construct(ObjectManager $objectManager, UiContext $context)
    {
        $this->objectManager = $objectManager;
        $this->context = $context;
    }

    /**
     * Create data provider
     *
     * @param $elementName
     * @param array $data
     * @throws \Exception
     * @return ElementInterface
     */
    public function create($elementName, array $data = [])
    {
        if ('text' == $elementName) {
            $elementName = 'input';
        }
        $block = $this->context->getLayout()->getBlock($elementName);
        if (!$block) {
            throw new \Exception('Can not find block of element ' . $elementName);
        }
        $newBlock = clone $block;
        $newBlock->addData($data);
        return $newBlock;
    }
}
