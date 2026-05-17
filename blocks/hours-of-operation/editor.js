/**
 * Hours of operation — block editor (wp global script, no build step).
 */
( function ( blocks, element, blockEditor, components, i18n ) {
	const { registerBlockType } = blocks;
	const { createElement: el, Fragment } = element;
	const { useBlockProps, InspectorControls } = blockEditor;
	const {
		PanelBody,
		ToggleControl,
		TextControl,
		Notice,
	} = components;
	const { __, sprintf } = i18n;

	const defaultSchedule = [
		{ dayIndex: 1, closed: false, openTime: '09:00', closeTime: '17:00' },
		{ dayIndex: 2, closed: false, openTime: '09:00', closeTime: '17:00' },
		{ dayIndex: 3, closed: false, openTime: '09:00', closeTime: '17:00' },
		{ dayIndex: 4, closed: false, openTime: '09:00', closeTime: '17:00' },
		{ dayIndex: 5, closed: false, openTime: '09:00', closeTime: '17:00' },
		{ dayIndex: 6, closed: false, openTime: '10:00', closeTime: '16:00' },
		{ dayIndex: 7, closed: true, openTime: '', closeTime: '' },
	];

	function sortSchedule( rows ) {
		const byDay = {};
		( rows && rows.length ? rows : defaultSchedule ).forEach( function ( r ) {
			const d = Math.min( 7, Math.max( 1, parseInt( r.dayIndex, 10 ) || 1 ) );
			byDay[ d ] = {
				dayIndex: d,
				closed: !! r.closed,
				openTime: typeof r.openTime === 'string' ? r.openTime : '',
				closeTime: typeof r.closeTime === 'string' ? r.closeTime : '',
			};
		} );
		const out = [];
		for ( let d = 1; d <= 7; d += 1 ) {
			out.push( byDay[ d ] || { dayIndex: d, closed: true, openTime: '', closeTime: '' } );
		}
		return out;
	}

	function weekdayAbbrev( dayIndex ) {
		const monday = new Date( Date.UTC( 2024, 0, 1, 12, 0, 0 ) );
		const ts = monday.getTime() + ( dayIndex - 1 ) * 86400000;
		const d = new Date( ts );
		return d.toLocaleDateString( undefined, { weekday: 'short', timeZone: 'UTC' } );
	}

	function weekdayFull( dayIndex ) {
		const monday = new Date( Date.UTC( 2024, 0, 1, 12, 0, 0 ) );
		const ts = monday.getTime() + ( dayIndex - 1 ) * 86400000;
		const d = new Date( ts );
		return d.toLocaleDateString( undefined, { weekday: 'long', timeZone: 'UTC' } );
	}

	function getTodayN() {
		const js = new Date().getDay();
		return js === 0 ? 7 : js;
	}

	function isHeaderInline( className ) {
		return (
			typeof className === 'string' &&
			className.indexOf( 'is-style-qbb-hours-header-inline' ) !== -1
		);
	}

	function findNextOpeningJs( schedule, afterDayN ) {
		for ( let step = 1; step <= 7; step += 1 ) {
			const n = ( ( afterDayN - 1 + step ) % 7 ) + 1;
			const row = schedule.find( function ( r ) {
				return r.dayIndex === n;
			} );
			if ( row && ! row.closed && row.openTime ) {
				return { dayIndex: n, openTime: row.openTime };
			}
		}
		return null;
	}

	function minutesFromHHMM( s ) {
		const m = /^([01]\d|2[0-3]):([0-5]\d)$/.exec( s );
		if ( ! m ) {
			return null;
		}
		return parseInt( m[ 1 ], 10 ) * 60 + parseInt( m[ 2 ], 10 );
	}

	function nowMinutesLocal() {
		const d = new Date();
		return d.getHours() * 60 + d.getMinutes();
	}

	function inlinePreviewStatus( schedule, todayN ) {
		const row = schedule.find( function ( r ) {
			return r.dayIndex === todayN;
		} );
		let primary = '';

		if ( ! row ) {
			primary = __( 'Check hours', 'queens-botanical-block' );
		} else if ( row.closed ) {
			primary = __( 'Closed today', 'queens-botanical-block' );
			const next = findNextOpeningJs( schedule, todayN );
			if ( next ) {
				primary = sprintf(
					/* translators: 1: abbreviated weekday, 2: time (24h in editor). */
					__( 'Closed today • Opens %1$s at %2$s', 'queens-botanical-block' ),
					weekdayAbbrev( next.dayIndex ),
					next.openTime
				);
			}
		} else if ( ! row.openTime || ! row.closeTime ) {
			primary = __( 'Hours posted soon', 'queens-botanical-block' );
		} else {
			const om = minutesFromHHMM( row.openTime );
			const cm = minutesFromHHMM( row.closeTime );
			const nm = nowMinutesLocal();
			if ( om === null || cm === null ) {
				primary = sprintf(
					__( 'Open today %1$s - %2$s', 'queens-botanical-block' ),
					row.openTime,
					row.closeTime
				);
			} else {
				let closeM = cm;
				if ( closeM <= om ) {
					closeM += 24 * 60;
				}
				if ( nm < om ) {
					primary = sprintf(
						__( 'Opens today at %s', 'queens-botanical-block' ),
						row.openTime
					);
				} else if ( nm > closeM ) {
					primary = __( 'Closed today', 'queens-botanical-block' );
					const next = findNextOpeningJs( schedule, todayN );
					if ( next ) {
						primary = sprintf(
							__( 'Closed today • Opens %1$s at %2$s', 'queens-botanical-block' ),
							weekdayAbbrev( next.dayIndex ),
							next.openTime
						);
					}
				} else {
					primary = sprintf(
						__( 'Open today %1$s - %2$s', 'queens-botanical-block' ),
						row.openTime,
						row.closeTime
					);
				}
			}
		}

		return el(
			'div',
			{
				className: 'qbb-hours__inline-status',
				key: 'inline-preview',
				role: 'status',
			},
			el( 'p', { className: 'qbb-hours__inline qbb-hours__inline--primary' }, primary )
		);
	}

	registerBlockType( 'queens-botanical-block/hours-of-operation', {
		edit: function ( props ) {
			const { attributes, setAttributes } = props;
			const schedule = sortSchedule( attributes.schedule );
			const todayN = getTodayN();
			const headerInline = isHeaderInline( attributes.className || '' );

			const updateRow = function ( index, patch ) {
				const next = schedule.slice();
				next[ index ] = Object.assign( {}, next[ index ], patch );
				setAttributes( { schedule: next } );
			};

			const blockProps = useBlockProps( {
				className:
					'qbb-hours qbb-hours--editor' +
					( headerInline ? '' : ' qbb-hours--garden' ),
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
						el( ToggleControl, {
							label: __( 'Show heading', 'queens-botanical-block' ),
							checked: !! attributes.showHeading,
							disabled: headerInline,
							onChange: function ( v ) {
								setAttributes( { showHeading: v } );
							},
						} ),
						attributes.showHeading && ! headerInline
							? el( TextControl, {
								label: __( 'Heading text', 'queens-botanical-block' ),
								help: __( 'Leave empty to use “Hours”.', 'queens-botanical-block' ),
								value: attributes.heading || '',
								onChange: function ( v ) {
									setAttributes( { heading: v } );
								},
							} )
							: null,
						el( ToggleControl, {
							label: __( 'Show “Today” summary', 'queens-botanical-block' ),
							checked: !! attributes.showTodaySummary,
							disabled: headerInline,
							onChange: function ( v ) {
								setAttributes( { showTodaySummary: v } );
							},
						} ),
						el( ToggleControl, {
							label: __( 'Highlight today in the list', 'queens-botanical-block' ),
							checked: !! attributes.highlightToday,
							disabled: headerInline,
							onChange: function ( v ) {
								setAttributes( { highlightToday: v } );
							},
						} )
					)
				),
				el(
					'div',
					blockProps,
					headerInline
						? inlinePreviewStatus( schedule, todayN )
						: null,
					! headerInline && attributes.showHeading
						? el(
							'h3',
							{
								className:
									'qbb-hours__heading wp-block-heading has-x-large-font-size',
							},
							attributes.heading && attributes.heading.trim() !== ''
								? attributes.heading
								: __( 'Hours', 'queens-botanical-block' )
						)
						: null,
					! headerInline && attributes.showTodaySummary
						? el(
							'p',
							{
								className: 'qbb-hours__today--editor-note',
								'aria-hidden': 'true',
							},
							__(
								'The “Today” callout uses your site timezone on the front end.',
								'queens-botanical-block'
							)
						)
						: null,
					el(
						'div',
						{ className: 'qbb-hours__list', role: 'list' },
						schedule.map( function ( row, index ) {
							const isToday =
								! headerInline &&
								!! attributes.highlightToday &&
								row.dayIndex === todayN;
							return el(
								'div',
								{
									key: row.dayIndex,
									className:
										'qbb-hours__row' + ( isToday ? ' is-today' : '' ),
									role: 'listitem',
								},
								el(
									'p',
									{ className: 'qbb-hours__day' },
									el(
										'span',
										{ className: 'qbb-hours__day-abbr', 'aria-hidden': 'true' },
										weekdayAbbrev( row.dayIndex )
									),
									el(
										'span',
										{ className: 'qbb-hours__sr-only' },
										weekdayFull( row.dayIndex )
									)
								),
								el(
									'div',
									{ className: 'qbb-hours__editor-row' },
									el( ToggleControl, {
										label: __( 'Closed', 'queens-botanical-block' ),
										checked: !! row.closed,
										onChange: function ( v ) {
											updateRow( index, { closed: v } );
										},
									} ),
									! row.closed
										? el(
											Fragment,
											null,
											el( TextControl, {
												label: __( 'Open', 'queens-botanical-block' ),
												type: 'time',
												value: row.openTime || '',
												onChange: function ( v ) {
													updateRow( index, { openTime: v } );
												},
											} ),
											el( TextControl, {
												label: __( 'Close', 'queens-botanical-block' ),
												type: 'time',
												value: row.closeTime || '',
												onChange: function ( v ) {
													updateRow( index, { closeTime: v } );
												},
											} )
										)
										: null
								)
							);
						} )
					),
					el(
						Notice,
						{ status: 'info', isDismissible: false },
						headerInline
							? __(
								'Live site uses your timezone and time format. Closed today + next opening appear after close or on closed days',
								'queens-botanical-block'
							)
							: __(
								'Times are saved as 24-hour values; the site time format is used on the public site.',
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
