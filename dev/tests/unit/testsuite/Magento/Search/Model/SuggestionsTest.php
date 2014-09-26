<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Search\Model;

use Magento\TestFramework\Helper\ObjectManager;

class SuggestionsTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ObjectManager
     */
    private $objectManager;

    protected function setUp()
    {
        $this->objectManager = new ObjectManager($this);
    }

    public function testGetSuggestions()
    {
        /** @var \Magento\Search\Model\Suggestions $suggestions */
        $suggestions = $this->objectManager->getObject('\Magento\Search\Model\Suggestions');
        $this->assertEquals([], $suggestions->getSuggestions('some text'));
    }

    public function testIsCountResultsEnabled()
    {
        /** @var \Magento\Search\Model\Suggestions $suggestions */
        $suggestions = $this->objectManager->getObject('\Magento\Search\Model\Suggestions');
        $this->assertFalse($suggestions->isCountResultsEnabled());
    }
}
