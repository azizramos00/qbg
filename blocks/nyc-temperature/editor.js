/**
 * NYC temperature — block editor (wp global script).
 */
( function ( blocks, element, blockEditor, components, i18n ) {
	const { registerBlockType } = blocks;
	const { createElement: el, Fragment } = element;
	const { useBlockProps, InspectorControls } = blockEditor;
	const { PanelBody, ToggleControl, RadioControl } = components;
	const { __ } = i18n;

	registerBlockType( 'queens-botanical-block/nyc-temperature', {
		edit: function ( props ) {
			const { attributes, setAttributes } = props;
			const blockProps = useBlockProps( {
				className: 'qbb-nyc-temp qbb-nyc-temp--editor',
			} );

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'Display', 'queens-botanical-block' ), initialOpen: true },
						el( RadioControl, {
							label: __( 'Units', 'queens-botanical-block' ),
							selected: attributes.unit || 'fahrenheit',
							options: [
								{ label: __( 'Fahrenheit (°F)', 'queens-botanical-block' ), value: 'fahrenheit' },
								{ label: __( 'Celsius (°C)', 'queens-botanical-block' ), value: 'celsius' },
							],
							onChange: function ( v ) {
								setAttributes( { unit: v } );
							},
						} ),
						el( ToggleControl, {
							label: __( 'Show “Flushing, NY” label', 'queens-botanical-block' ),
							checked: !! attributes.showLocationLabel,
							onChange: function ( v ) {
								setAttributes( { showLocationLabel: v } );
							},
						} ),
						el( ToggleControl, {
							label: __( 'Show National Weather Service link', 'queens-botanical-block' ),
							checked: !! attributes.showAttribution,
							onChange: function ( v ) {
								setAttributes( { showAttribution: v } );
							},
						} )
					)
				),
				el(
					'div',
					blockProps,
					attributes.showLocationLabel
						? el(
							'p',
							{ className: 'qbb-nyc-temp__label' },
							__( 'Flushing, NY', 'queens-botanical-block' )
						)
						: null,
					el(
						'p',
						{ className: 'qbb-nyc-temp__value' },
						__( '° — live on site', 'queens-botanical-block' )
					),
					el(
						'p',
						{
							className: 'qbb-nyc-temp__hint',
							style: { fontSize: '0.8125rem', marginTop: '0.5rem', opacity: 0.8 },
						},
						__(
							'Temperature loads from weather.gov (Central Park / KNYC) on the front end and is cached for a few minutes.',
							'queens-botanical-block'
						)
					)
				)
			);
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.components, window.wp.i18n );
