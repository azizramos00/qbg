/**
 * Program sidebar CTA — editor placeholder (fields live in Page sidebar panel).
 */
( function ( element, blockEditor, components, data, i18n ) {
	const { createElement: el } = element;
	const { useBlockProps } = blockEditor;
	const { Placeholder, ExternalLink } = components;
	const { useSelect } = data;
	const { __ } = i18n;

	window.qbbRegisterDynamicBlockEdit( 'queens-botanical-block/program-sidebar-cta', {
		edit: function () {
			const blockProps = useBlockProps( {
				className: 'qbb-program-sidebar-cta-editor',
			} );

			const { meta, postType } = useSelect( function ( select ) {
				const editor = select( 'core/editor' );
				const type = editor.getCurrentPostType();
				const id = editor.getCurrentPostId();
				if ( type !== 'page' || ! id ) {
					return { meta: {}, postType: type };
				}
				const record = select( 'core' ).getEntityRecord( 'postType', 'page', id );
				return {
					meta: record && record.meta ? record.meta : {},
					postType: type,
				};
			}, [] );

			const heading = meta.qbb_program_cta_heading || '';
			const secondaryOn =
				meta.qbb_program_cta_secondary_enabled === true ||
				meta.qbb_program_cta_secondary_enabled === '1' ||
				( meta.qbb_program_cta_secondary_url &&
					meta.qbb_program_cta_secondary_enabled !== false &&
					meta.qbb_program_cta_secondary_enabled !== '0' );
			const hasContent =
				postType === 'page' &&
				( heading ||
					meta.qbb_program_cta_primary_url ||
					( secondaryOn && meta.qbb_program_cta_secondary_url ) );

			const instructions =
				postType === 'page'
					? __(
							'Edit heading, copy, and buttons in the Page sidebar under “Program sidebar CTA”. Turn on “Add second button” when you need two links.',
							'queens-botanical-block'
					  )
					: __(
							'This block reads per-page fields. Open a Programs page to edit the CTA, or use the template part preview only.',
							'queens-botanical-block'
					  );

			return el(
				Placeholder,
				Object.assign( {}, blockProps, {
					label: __( 'Program sidebar CTA', 'queens-botanical-block' ),
					instructions: instructions,
				} ),
				hasContent
					? el(
							'div',
							{ className: 'qbb-program-sidebar-cta-editor__preview' },
							el( 'strong', null, heading || __( '(No heading yet)', 'queens-botanical-block' ) ),
							meta.qbb_program_cta_primary_url
								? el(
										'p',
										null,
										el(
											ExternalLink,
											{ href: meta.qbb_program_cta_primary_url },
											meta.qbb_program_cta_primary_label ||
												meta.qbb_program_cta_primary_url
										)
								  )
								: null,
							secondaryOn && meta.qbb_program_cta_secondary_url
								? el(
										'p',
										null,
										el(
											ExternalLink,
											{ href: meta.qbb_program_cta_secondary_url },
											meta.qbb_program_cta_secondary_label ||
												meta.qbb_program_cta_secondary_url
										)
								  )
								: null
					  )
					: null
			);
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp.element, window.wp.blockEditor, window.wp.components, window.wp.data, window.wp.i18n );
