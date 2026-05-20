/**
 * Page editor sidebar: Program description (post meta).
 */
( function ( plugins, editPost, components, data, element, i18n ) {
	const { registerPlugin } = plugins;
	const { PluginDocumentSettingPanel } = editPost;
	const { TextareaControl } = components;
	const { useSelect, useDispatch } = data;
	const { createElement: el } = element;
	const { __ } = i18n;

	const META_KEY = 'qbb_program_description';

	function isProgramsPage( postType, template ) {
		return (
			postType === 'page' &&
			window.qbbProgramSidebar &&
			window.qbbProgramSidebar.isProgramsTemplate( template )
		);
	}

	function ProgramDescriptionPanel() {
		const { meta, postType, template } = useSelect( function ( select ) {
			const editor = select( 'core/editor' );
			return {
				meta: editor.getEditedPostAttribute( 'meta' ) || {},
				postType: editor.getCurrentPostType(),
				template: editor.getEditedPostAttribute( 'template' ) || '',
			};
		}, [] );

		const { editPost: edit } = useDispatch( 'core/editor' );

		if ( ! isProgramsPage( postType, template ) ) {
			return null;
		}

		function setMeta( value ) {
			edit( { meta: { [ META_KEY ]: value } } } );
		}

		return el(
			PluginDocumentSettingPanel,
			{
				name: 'qbb-program-description',
				title: __( 'Program description', 'queens-botanical-block' ),
				icon: 'text',
			},
			el(
				'p',
				{ className: 'qbb-program-description-panel__help' },
				__(
					'Short intro below the hero and above the content columns. Save the page after editing. Appears on the front end only when this field has text.',
					'queens-botanical-block'
				)
			),
			el( TextareaControl, {
				label: __( 'Description', 'queens-botanical-block' ),
				value: meta[ META_KEY ] || '',
				rows: 5,
				onChange: setMeta,
			} )
		);
	}

	registerPlugin( 'qbb-program-description', {
		render: ProgramDescriptionPanel,
		icon: 'text',
	} );
} )(
	window.wp.plugins,
	window.wp.editPost,
	window.wp.components,
	window.wp.data,
	window.wp.element,
	window.wp.i18n
);
