/**
 * Merge block.json metadata with a client edit/save implementation.
 *
 * Calling registerBlockType() with only edit/save wipes apiVersion and supports
 * from the block.json registration and triggers "Attempt recovery" in the editor.
 */
( function ( blocks ) {
	/**
	 * @param {string} blockName Block name (namespace/block).
	 * @param {Object} settings   edit, save, and other overrides.
	 */
	window.qbbRegisterDynamicBlockEdit = function ( blockName, settings ) {
		var existing = blocks.getBlockType( blockName );
		var merged = Object.assign( {}, existing || {}, settings || {} );

		if ( existing ) {
			blocks.unregisterBlockType( blockName );
		}

		blocks.registerBlockType( blockName, merged );
	};
} )( window.wp.blocks );
