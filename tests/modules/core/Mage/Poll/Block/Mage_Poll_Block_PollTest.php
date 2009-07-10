<?php
class Mage_Poll_Block_PollTest extends Mage_TestCase
{
    public function testConstructor()
    {
        $block = new Mage_Poll_Block_Poll();
        $defaultTemplate = $block->getTemplate();
        $this->assertNotNull($defaultTemplate, "Block doesn't have default template defined");
    }
}
