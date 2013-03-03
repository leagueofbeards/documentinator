Annotator.Plugin.Avatar = function() {
	var plugin = {};

	plugin.pluginInit = function () {
		this.annotator.viewer.addField({
			load: function (field, annotation) {
				if( annotation.avatar ) {
					field.innerHTML = annotation.avatar + '&nbsp;&nbsp;' + annotation.user;
				} else {
					field.innerHTML = DI.avatar + '&nbsp;&nbsp;' + DI.username;
				}
			}
		});
	};

	return plugin;
}