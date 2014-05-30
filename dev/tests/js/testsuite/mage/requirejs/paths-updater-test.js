/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint globalstrict: true*/

"use strict";

/*jshint undef: false, newcap: false*/
var PathsUpdaterTest = TestCase('PathsUpdaterTest');

PathsUpdaterTest.prototype.testUpdateConfigPaths = function() {
    var config = {paths: { simplePath: "simple/path.js", relativePath: "./relative/path.js" }};
    var expected = {paths: { simplePath: "simple/path.js", relativePath: "relative/path.js" }};
    assertEquals(expected, mageUpdateConfigPaths(config, ''));
};

PathsUpdaterTest.prototype.testUpdateConfigPathsWithContext = function() {
    var config = {paths: { simplePath: "simple/path.js", relativePath: "./relative/path.js" }};
    var context = 'context';
    var expectedWithContext = {paths: { simplePath: "simple/path.js", relativePath: "context/relative/path.js" }};
    assertEquals(expectedWithContext, mageUpdateConfigPaths(config, context));
};
