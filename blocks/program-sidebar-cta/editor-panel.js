/**
 * Page editor sidebar: per-page Programs CTA fields (post meta).
 */
( function ( plugins, editPost, components, data, element, i18n ) {
	const { registerPlugin } = plugins;
	const { PluginDocumentSettingPanel } = editPost;
	const { TextControl, TextareaControl, ToggleControl } = components;
	const { useSelect, useDispatch } = data;
	const { createElement: el, Fragment } = element;
	const { __ } = i18n;

	const META_HEADING = 'qbb_program_cta_heading';
	const META_DESCRIPTION = 'qbb_program_cta_description';
	const META_PRIMARY_LABEL = 'qbb_program_cta_primary_label';
	const META_PRIMARY_URL = 'qbb_program_cta_primary_url';
	const META_SECONDARY_ENABLED = 'qbb_program_cta_secondary_enabled';
	const META_SECONDARY_LABEL = 'qbb_program_cta_secondary_label';
	const META_SECONDARY_URL = 'qbb_program_cta_secondary_url';

	function isProgramsPage( postType, template ) {
		return (
			postType === 'page' &&
			window.qbbProgramSidebar &&
			window.qbbProgramSidebar.isProgramsTemplate( template )
		);
	}

	function ProgramCtaPanel() {
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

		const secondaryEnabled =
			meta[ META_SECONDARY_ENABLED ] === true ||
			meta[ META_SECONDARY_ENABLED ] === '1' ||
			( meta[ META_SECONDARY_ENABLED ] !== false &&
				meta[ META_SECONDARY_ENABLED ] !== '0' &&
				!! meta[ META_SECONDARY_URL ] );

		function setMeta( key, value ) {
			edit( { meta: { [ key ]: value } } );
		}

		function setSecondaryEnabled( enabled ) {
			if ( ! enabled ) {
				edit( {
					meta: {
						[ META_SECONDARY_ENABLED ]: false,
						[ META_SECONDARY_LABEL ]: '',
						[ META_SECONDARY_URL ]: '',
					},
				} );
				return;
			}
			setMeta( META_SECONDARY_ENABLED, true );
		}

		return el(
			PluginDocumentSettingPanel,
			{
				name: 'qbb-program-sidebar-cta',
				title: __( 'Program sidebar CTA', 'queens-botanical-block' ),
				icon: 'megaphone',
			},
			el(
				'p',
				{ className: 'qbb-program-cta-panel__help' },
				__(
					'Used in the Programs template sidebar. Shared layout, unique copy and links per page. Booking steps are edited under “Steps to booking”.',
					'queens-botanical-block'
				)
			),
			el( TextControl, {
				label: __( 'Heading', 'queens-botanical-block' ),
				value: meta[ META_HEADING ] || '',
				placeholder: 'Spring & Summer 2026 Registration Now Open!',
				onChange: function ( v ) {
					setMeta( META_HEADING, v );
				},
			} ),
			el( TextareaControl, {
				label: __( 'Description', 'queens-botanical-block' ),
				value: meta[ META_DESCRIPTION ] || '',
				placeholder:
					'Request aid on a sliding scale via Brightwheel when you register…',
				onChange: function ( v ) {
					setMeta( META_DESCRIPTION, v );
				},
			} ),
			el( TextControl, {
				label: __( 'Primary button label', 'queens-botanical-block' ),
				value: meta[ META_PRIMARY_LABEL ] || '',
				placeholder: 'Learn more',
				onChange: function ( v ) {
					setMeta( META_PRIMARY_LABEL, v );
				},
			} ),
			el( TextControl, {
				label: __( 'Primary button URL', 'queens-botanical-block' ),
				value: meta[ META_PRIMARY_URL ] || '',
				type: 'url',
				onChange: function ( v ) {
					setMeta( META_PRIMARY_URL, v );
				},
			} ),
			el( ToggleControl, {
				label: __( 'Add second button', 'queens-botanical-block' ),
				help: __(
					'Show an optional outline button next to the primary button.',
					'queens-botanical-block'
				),
				checked: secondaryEnabled,
				onChange: setSecondaryEnabled,
			} ),
			secondaryEnabled
				? el(
						Fragment,
						null,
						el( TextControl, {
							label: __( 'Second button label', 'queens-botanical-block' ),
							value: meta[ META_SECONDARY_LABEL ] || '',
							placeholder: __( 'Call us', 'queens-botanical-block' ),
							onChange: function ( v ) {
								setMeta( META_SECONDARY_LABEL, v );
							},
						} ),
						el( TextControl, {
							label: __( 'Second button URL', 'queens-botanical-block' ),
							value: meta[ META_SECONDARY_URL ] || '',
							type: 'url',
							onChange: function ( v ) {
								setMeta( META_SECONDARY_URL, v );
							},
						} )
				  )
				: null
		);
	}

	registerPlugin( 'qbb-program-sidebar-cta', {
		render: ProgramCtaPanel,
	} );
} )(
	window.wp.plugins,
	window.wp.editPost,
	window.wp.components,
	window.wp.data,
	window.wp.element,
	window.wp.i18n
);
