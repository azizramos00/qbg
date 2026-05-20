/**
 * Page editor sidebar: per-page Steps to booking fields (post meta).
 */
( function ( plugins, editPost, components, data, element, i18n ) {
	const { registerPlugin } = plugins;
	const { PluginDocumentSettingPanel } = editPost;
	const { TextControl, TextareaControl, Button, PanelBody } = components;
	const { useSelect, useDispatch } = data;
	const { createElement: el, Fragment } = element;
	const { __, sprintf } = i18n;

	const META_HEADING = 'qbb_program_booking_heading';
	const META_DESCRIPTION = 'qbb_program_booking_description';
	const META_STEPS = 'qbb_program_booking_steps';
	const MAX_STEPS = 12;

	function normalizeSteps( raw ) {
		if ( ! Array.isArray( raw ) ) {
			return [];
		}
		return raw.map( function ( step ) {
			return {
				title: step && step.title ? String( step.title ) : '',
				description: step && step.description ? String( step.description ) : '',
			};
		} );
	}

	function isProgramsPage( postType, template ) {
		return (
			postType === 'page' &&
			window.qbbProgramSidebar &&
			window.qbbProgramSidebar.isProgramsTemplate( template )
		);
	}

	function BookingStepsPanel() {
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

		const steps = normalizeSteps( meta[ META_STEPS ] );

		function setMeta( key, value ) {
			edit( { meta: { [ key ]: value } } );
		}

		function setSteps( nextSteps ) {
			setMeta( META_STEPS, nextSteps );
		}

		function updateStep( index, field, value ) {
			const next = steps.map( function ( step, i ) {
				if ( i !== index ) {
					return step;
				}
				return Object.assign( {}, step, { [ field ]: value } );
			} );
			setSteps( next );
		}

		function addStep() {
			if ( steps.length >= MAX_STEPS ) {
				return;
			}
			setSteps( steps.concat( [ { title: '', description: '' } ] ) );
		}

		function removeStep( index ) {
			setSteps( steps.filter( function ( _, i ) {
				return i !== index;
			} ) );
		}

		return el(
			PluginDocumentSettingPanel,
			{
				name: 'qbb-program-booking-steps',
				title: __( 'Steps to booking', 'queens-botanical-block' ),
				icon: 'list-view',
			},
			el(
				'p',
				{ className: 'qbb-program-booking-panel__help' },
				__(
					'Used in the Programs template sidebar. Same layout on every Programs page; each page has its own steps and copy.',
					'queens-botanical-block'
				)
			),
			el( TextControl, {
				label: __( 'Card heading', 'queens-botanical-block' ),
				value: meta[ META_HEADING ] || '',
				placeholder: __( 'Steps to booking', 'queens-botanical-block' ),
				onChange: function ( v ) {
					setMeta( META_HEADING, v );
				},
			} ),
			el( TextareaControl, {
				label: __( 'Card description', 'queens-botanical-block' ),
				value: meta[ META_DESCRIPTION ] || '',
				placeholder: __(
					'Browse the program that fits right for your child',
					'queens-botanical-block'
				),
				onChange: function ( v ) {
					setMeta( META_DESCRIPTION, v );
				},
			} ),
			el(
				'div',
				{ className: 'qbb-program-booking-panel__steps' },
				el(
					'p',
					{ className: 'components-base-control__label' },
					sprintf(
						/* translators: %d: maximum number of steps */
						__( 'Steps (%d max)', 'queens-botanical-block' ),
						MAX_STEPS
					)
				),
				steps.length === 0
					? el(
							'p',
							{ className: 'qbb-program-booking-panel__empty' },
							__( 'No steps yet. Add a step below.', 'queens-botanical-block' )
					  )
					: steps.map( function ( step, index ) {
							return el(
								PanelBody,
								{
									key: 'step-' + index,
									title: step.title
										? step.title
										: sprintf(
												/* translators: %d: step number */
												__( 'Step %d', 'queens-botanical-block' ),
												index + 1
										  ),
									initialOpen: index === 0,
								},
								el( TextControl, {
									label: __( 'Step title', 'queens-botanical-block' ),
									value: step.title,
									onChange: function ( v ) {
										updateStep( index, 'title', v );
									},
								} ),
								el( TextareaControl, {
									label: __( 'Step description', 'queens-botanical-block' ),
									value: step.description,
									onChange: function ( v ) {
										updateStep( index, 'description', v );
									},
								} ),
								el(
									Button,
									{
										isDestructive: true,
										variant: 'secondary',
										onClick: function () {
											removeStep( index );
										},
									},
									__( 'Remove step', 'queens-botanical-block' )
								)
							);
					  } ),
				el(
					Button,
					{
						variant: 'primary',
						onClick: addStep,
						disabled: steps.length >= MAX_STEPS,
						style: { marginTop: '8px' },
					},
					__( 'Add step', 'queens-botanical-block' )
				)
			)
		);
	}

	registerPlugin( 'qbb-program-booking-steps', {
		render: BookingStepsPanel,
		icon: 'list-view',
	} );
} )(
	window.wp.plugins,
	window.wp.editPost,
	window.wp.components,
	window.wp.data,
	window.wp.element,
	window.wp.i18n
);
