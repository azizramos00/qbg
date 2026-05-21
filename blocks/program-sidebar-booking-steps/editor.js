/**
 * Program sidebar — Steps to booking (editor placeholder; fields in Page sidebar panel).
 */
( function ( element, blockEditor, components, data, i18n ) {
	const { createElement: el } = element;
	const { useBlockProps } = blockEditor;
	const { Placeholder } = components;
	const { useSelect } = data;
	const { __, sprintf, _n } = i18n;

	window.qbbRegisterDynamicBlockEdit( 'queens-botanical-block/program-sidebar-booking-steps', {
		edit: function () {
			const blockProps = useBlockProps( {
				className: 'qbb-program-booking-steps-editor',
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

			const heading = meta.qbb_program_booking_heading || '';
			const steps = Array.isArray( meta.qbb_program_booking_steps )
				? meta.qbb_program_booking_steps
				: [];
			const stepCount = steps.filter( function ( s ) {
				return s && ( s.title || s.description );
			} ).length;
			const hasContent = postType === 'page' && ( heading || stepCount > 0 );

			const instructions =
				postType === 'page'
					? __(
							'Edit the card heading, description, and steps in the Page sidebar under “Steps to booking”. Add or remove steps per program page.',
							'queens-botanical-block'
					  )
					: __(
							'This block reads per-page fields. Open a Programs page to edit steps, or use the template part preview only.',
							'queens-botanical-block'
					  );

			return el(
				Placeholder,
				Object.assign( {}, blockProps, {
					label: __( 'Steps to booking', 'queens-botanical-block' ),
					instructions: instructions,
				} ),
				hasContent
					? el(
							'div',
							{ className: 'qbb-program-booking-steps-editor__preview' },
							el(
								'strong',
								null,
								heading || __( '(No card heading yet)', 'queens-botanical-block' )
							),
							el(
								'p',
								null,
								sprintf(
									/* translators: %d: number of steps */
									_n(
										'%d step configured',
										'%d steps configured',
										stepCount,
										'queens-botanical-block'
									),
									stepCount
								)
							)
					  )
					: null
			);
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp.element, window.wp.blockEditor, window.wp.components, window.wp.data, window.wp.i18n );
