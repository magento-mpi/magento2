define([
	'angular',
	'./app'
], function(ng){
	'use strict';

	return function(el, config){
		ng.bootstrap(el, ['app']);
	}
});