/**
 * Shared helpers for Programs sidebar document panels.
 */
( function () {
	/**
	 * theme.json customTemplates[].name for templates/programs.html
	 */
	var TEMPLATE_SLUG = 'programs';

	function isProgramsTemplate( template ) {
		if ( ! template || typeof template !== 'string' ) {
			return false;
		}
		if ( template === TEMPLATE_SLUG ) {
			return true;
		}
		var parts = template.split( '/' );
		return parts[ parts.length - 1 ] === TEMPLATE_SLUG;
	}

	window.qbbProgramSidebar = {
		TEMPLATE_SLUG: TEMPLATE_SLUG,
		isProgramsTemplate: isProgramsTemplate,
	};
} )();
