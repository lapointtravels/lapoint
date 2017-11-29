if (typeof jQuery === 'function') {
	define('jquery', function () { return jQuery; });
}

if (typeof _ === 'function') {
	define('underscore', function () { return _; });
}
if (typeof Backbone === 'object') {
	define('backbone', function () { return Backbone; });
}

requirejs.config({
	baseUrl: theme_url + '/js/vendor',
    urlArgs: "bust=v18",
	paths: {
		app: '../app',
        TweenLite: './greensock/TweenLite.min',
        CSSPlugin: './greensock/CSSPlugin.min',
        Select2: './select2.min'
        // iframeResizer: './iframeResizer.min'
	},
    shim: {
        "backbone": {
            deps: ["underscore", "jquery"],
            exports: "Backbone"
        }
    }
});


requirejs(['app/main']);