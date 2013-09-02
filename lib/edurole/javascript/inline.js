
var Aloha = window.Aloha || ( window.Aloha = {} );
		
	Aloha.settings = {
		logLevels: { 'error': true, 'warn': true, 'info': true, 'debug': false, 'deprecated': true },
		errorhandling: false,
		ribbon: {enable: true},
		locale: 'en',
		plugins: {
		format: {
			config: [  'b', 'i', 'p', 'sub', 'sup', 'del', 'title', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'pre', 'removeFormat' ]
		}
		},
		sidebar: {
			disabled: true
		}
	};
