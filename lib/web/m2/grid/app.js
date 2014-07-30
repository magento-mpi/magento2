define([
	'angular',
	'./controllers/index'
], function(ng){
	var app = ng.module('app', [
		'app.controllers'
	]);

	function init(el, config){
		app.value('config', config);

		ng.bootstrap(el, ['app']);
	}

	init.orig = app;

	return init;
});