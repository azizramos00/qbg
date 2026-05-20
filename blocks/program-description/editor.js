/**
 * Program description — editor placeholder (field in Page sidebar panel).
 */
( function ( blocks, element, blockEditor, components, data, i18n ) {
	const { registerBlockType } = blocks;
	const { createElement: el } = element;
	const { useBlockProps } = blockEditor;
	const { Placeholder } = components;
	const { useSelect } = data;
	const { __ } = i18n;

	function isProgramsTemplate( template ) {
		return (
			window.qbbProgramSidebar &&
			window.qbbProgramSidebar.isProgramsTemplate( template )
		);
	}

	registerBlockType( 'queens-botanical-block/program-description', {
		edit: function () {
			const blockProps = useBlockProps( {
				className: 'qbb-program-description-editor',
			} );

			const { description, postType, template } = useSelect( function ( select ) {
				const editor = select( 'core/editor' );
				const type = editor.getCurrentPostType();
				const id = editor.getCurrentPostId();
				const tpl = editor.getEditedPostAttribute( 'template' ) || '';
				if ( type !== 'page' || ! id ) {
					return { description: '', postType: type, template: tpl };
				}
				const record = select( 'core' ).getEntityRecord( 'postType', 'page', id );
				const meta = record && record.meta ? record.meta : {};
				return {
					description: meta.qbb_program_description || '',
					postType: type,
					template: tpl,
				};
			}, [] );

			const onProgramsPage = postType === 'page' && isProgramsTemplate( template );
			const instructions = onProgramsPage
				? __(
						'Edit the intro description in the Page sidebar under “Program description”. It appears below the hero.',
						'queens-botanical-block'
				  )
				: __(
						'This block only outputs content on Programs template pages.',
						'queens-botanical-block'
				  );

			return el(
				Placeholder,
				Object.assign( {}, blockProps, {
					label: __( 'Program description', 'queens-botanical-block' ),
					instructions: instructions,
				} ),
				onProgramsPage && description
					? el(
							'p',
							{ className: 'qbb-program-description-editor__preview' },
							description
					  )
					: null
			);
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.components, window.wp.data, window.wp.i18n );
