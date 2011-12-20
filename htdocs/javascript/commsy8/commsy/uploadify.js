/**
 * Uploadify Module
 */

define([	"order!libs/jQuery/jquery-1.7.1.min",
        	"order!libs/jQuery_plugins/uploadify-v2.1.4/swfobject",
        	"order!libs/jQuery_plugins/uploadify-v2.1.4/jquery.uploadify.v2.1.4.min",
        	"commsy/commsy_functions_8_0_0"], function() {
	return {
		options: {
			uploader:		'javascript/commsy8/libs/jQuery_plugins/uploadify-v2.1.4/uploadify.swf',
			method:			'GET',
			folder:			'javascript/commsy8/libs/jQuery_plugins/uploadify-v2.1.4/uploads',
			multi:			true,
			wmode:			'transparent',
			width:			160,
			height:			25,
			sizeLimit:		0,
			cancelImg:		'',
			onComplete: 	this.onComplete,
			onAllComplete:	this.onAllComplete,
			onError:		this.onError
		},
		
		init: function(commsy_functions, object, parameters) {
			parameters.object = object;
			parameters.handle = this;
			parameters.commsy_functions = commsy_functions;
			
			// set preconditions
			this.setPreconditions(commsy_functions, this.create, parameters);
		},
		
		setPreconditions: function(commsy_functions, callback, parameters) {
			var preconditions = {
					template: ['tpl_path'],
					environment: ['lang', 'single_entry_point', 'max_upload_size'],
					global: ['virus_scan', 'virus_scan_cron'],
					security: ['token']
			};
			
			// register preconditions
			commsy_functions.registerPreconditions(preconditions, callback, parameters);
		},
		
		create: function(preconditions, parameters) {
			var handle = parameters.handle;
			var object = parameters.object;
			var commsy_functions = parameters.commsy_functions;
			
			// create data object
			var data = new Object;
			data.cid = commsy_functions.getURLParam('cid');
			data.mod = 'ajax';
			data.fct = 'uploadify';
			data.c_virus_scan = preconditions.environment.virus_scan;
			data.c_virus_scan_cron = preconditions.environment.virus_scan_cron;
			
			var mod = commsy_functions.getURLParam('mod');
			var fct = commsy_functions.getURLParam('fct');
			var target_module = mod;
			if(mod === 'todo' && fct === 'detail') {
				target_module = 'step';
			} else if(mod === 'discussion' && fct == 'detail') {
				target_module = 'discarticle';
			}
			
			data.file_upload_rubric = target_module;
			data.SID = jQuery.cookie('SID');
			data.security_token = preconditions.security.token;
			
			// complete options
			handle.options.script = preconditions.environment.single_entry_point;
			handle.options.buttonImg = preconditions.template.tpl_path + '/img/uploadify/button_browse_' + preconditions.environment.lang + '.png';
			handle.options.sizeLimit = preconditions.environment.max_upload_size;
			handle.options.scriptData = data;
			
			// create
			object.uploadify(handle.options);
			
			// event handling
			parameters.upload_object.click(function() {
				object.uploadifyUpload();
			});
			parameters.clear_object.click(function() {
				object.uploadifyClearQueue();
			});
		},
		
		onComplete: function() {
			
		},
		
		onAllComplete: function() {
			
		},
		
		onError: function() {
			
		}
	};
});