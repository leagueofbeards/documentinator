Annotator.Plugin.Avatar = function (element, message) {
	var plugin = {};

	plugin.pluginInit = function () {
		this.annotator.viewer.addField({
			load: function (field, annotation) {
				field.innerHTML = annotation.avatar + '&nbsp;&nbsp;' + annotation.user;
			}
		});
	};

	return plugin;
}