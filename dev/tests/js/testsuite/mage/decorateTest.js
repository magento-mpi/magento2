/**
 * {license_notice}
 *
 * @category    mage.decorator
 * @package     test
 * @copyright   {copyright}
 * @license     {license_link}
 */
DecoratorTest = TestCase('DecoratorTest');
DecoratorTest.prototype.testDecoratorList = function () {
    /*:DOC += <ul id="list">
     <li>item1</li>
     <li>item2</li>
     <li>item3</li>
     <li>item4</li>
     </ul>
     */
    var listId = '#list';
    assertNotNull($(listId));
    mage.decorator.list(listId);
    assertTrue($($(listId).find('li')[0]).hasClass('odd'));
    assertFalse($($(listId).find('li')[0]).hasClass('even'));
    assertTrue($($(listId).find('li')[1]).hasClass('even'));
    assertFalse($($(listId).find('li')[1]).hasClass('odd'));
    assertTrue($($(listId).find('li')[2]).hasClass('odd'));
    assertFalse($($(listId).find('li')[2]).hasClass('even'));
    assertTrue($($(listId).find('li')[3]).hasClass('even'));
    assertFalse($($(listId).find('li')[3]).hasClass('odd'));
    assertTrue($($(listId).find('li')[3]).hasClass('last'));
};

DecoratorTest.prototype.testDecoratorGeneral = function () {
    /*:DOC += <div id="foo">
     <div class="item even">item1</div>
     <div class="item odd">item2</div>
     <div class="item odd">item3</div>
     <div class="item even">item4</div>
     </div>
     */
    var itemClass = '.item';
    mage.decorator.general($(itemClass));
    assertTrue($($(itemClass)[0]).hasClass('odd'));
    assertFalse($($(itemClass)[0]).hasClass('even'));
    assertTrue($($(itemClass)[0]).hasClass('first'));
    assertFalse($($(itemClass)[0]).hasClass('last'));

    assertFalse($($(itemClass)[1]).hasClass('odd'));
    assertTrue($($(itemClass)[1]).hasClass('even'));
    assertFalse($($(itemClass)[1]).hasClass('first'));
    assertFalse($($(itemClass)[1]).hasClass('last'));

    assertTrue($($(itemClass)[2]).hasClass('odd'));
    assertFalse($($(itemClass)[2]).hasClass('even'));
    assertFalse($($(itemClass)[2]).hasClass('first'));
    assertFalse($($(itemClass)[2]).hasClass('last'));

    assertFalse($($(itemClass)[3]).hasClass('odd'));
    assertTrue($($(itemClass)[3]).hasClass('even'));
    assertFalse($($(itemClass)[3]).hasClass('first'));
    assertTrue($($(itemClass)[3]).hasClass('last'));
};
