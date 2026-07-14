/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';
import {
	Button,
	SelectControl,
	PanelBody,
	ToggleControl,
	TextControl,
} from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';
import { useEffect, useState } from '@wordpress/element';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @param {Object}   root0               Component props.
 * @param {Object}   root0.attributes    Block attributes.
 * @param {Function} root0.setAttributes Attribute setter.
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {JSX.Element} Element to render.
 */
export default function Edit( { attributes, setAttributes } ) {
	const {
		selectedGame,
		questVersion,
		organization,
		version,
		popup,
		popupAuto,
		popupDelay,
		popupLimit,
		popupDisableDismiss,
		popupDisableNoscroll,
		popupAttach,
		popupTriggerButton,
		popupTriggerButtonLabel,
		popupTriggerButtonLabelMobile,
	} = attributes;

	// Initialize the state for the games and text.
	const [ games, setGames ] = useState( [] );
	const [ text, setText ] = useState( __( '', 'jquest' ) );
	const [ isRefreshing, setIsRefreshing ] = useState( false );

	const versions = [ 'stable', 'latest', 'v2' ];

	const versionOptions = versions.map( ( v ) => ( {
		label: v.charAt( 0 ).toUpperCase() + v.slice( 1 ),
		value: v,
	} ) );

	/**
	 * Updates the block with a games API response.
	 *
	 * @param {Object} data Games API response.
	 */
	const updateGames = ( data ) => {
		// Set information texts if no games or organization is found.
		if ( ! data.organization ) {
			setGames( [] );
			setText(
				__(
					'Organization not set. Set organization in JQUEST settings',
					'jquest'
				)
			);
			return;
		}

		setAttributes( { organization: data.organization } );

		if ( ! data.games?.length ) {
			setGames( [] );
			setText( __( 'No games found for organization.', 'jquest' ) );
			return;
		}

		// Map the games to an array of objects with value and label properties.
		const gameOptions = data.games.map( ( game ) => {
			const gameVersion = game.version === 'v2' ? 'v2' : 'v1';

			return {
				value: game.id,
				label:
					gameVersion === 'v2' ? `${ game.title } [v2]` : game.title,
				title: game.title,
				version: gameVersion,
			};
		} );
		setGames( gameOptions );

		const selectedGameOption =
			gameOptions.find( ( game ) => game.value === selectedGame ) ||
			gameOptions[ 0 ];

		setAttributes( {
			...( selectedGameOption.value !== selectedGame && {
				selectedGame: selectedGameOption.value,
			} ),
			questVersion: selectedGameOption.version,
			...( selectedGameOption.version === 'v2' && {
				version: 'v2',
			} ),
		} );
	};

	// Use the useEffect hook to fetch the games when the component mounts.
	useEffect( () => {
		apiFetch( {
			path: '/jquest/v1/games',
		} )
			.then( updateGames )
			.catch( () => {
				setText( __( 'Unable to load quests.', 'jquest' ) );
			} );
		// eslint-disable-next-line react-hooks/exhaustive-deps
	}, [] );

	/**
	 * Refreshes the organization's quests from the jQuest API.
	 */
	const refreshGames = () => {
		setIsRefreshing( true );
		apiFetch( {
			path: '/jquest/v1/games/refresh',
			method: 'POST',
		} )
			.then( updateGames )
			.catch( () => {
				setText( __( 'Unable to refresh quests.', 'jquest' ) );
			} )
			.finally( () => {
				setIsRefreshing( false );
			} );
	};

	// Show the selected game label in the block.
	useEffect( () => {
		games.forEach( ( game ) => {
			if ( game.value === selectedGame ) {
				// eslint-disable-next-line @wordpress/i18n-no-variables
				setText( __( game.title, 'jquest' ) );
			}
		} );
	}, [ selectedGame, organization, games ] );

	/**
	 * The `onChangeGame` function is called when the selected game changes.
	 * It sets the selected game attribute to the new game.
	 *
	 * @param {string} newGame - The new selected game.
	 */
	const onChangeGame = ( newGame ) => {
		const selectedGameOption = games.find(
			( game ) => game.value === newGame
		);
		setAttributes( {
			selectedGame: newGame,
			questVersion: selectedGameOption?.version || 'v1',
			...( selectedGameOption?.version === 'v2' && {
				version: 'v2',
			} ),
		} );
	};

	const openDashboard = () => {
		const buildUrl = ( org, quest, selectedQuestVersion ) => {
			return `https://dashboard.jquest.fi/#/dashboard/${ org }/${
				selectedQuestVersion === 'v2' ? 'quests/' : ''
			}${ quest }`;
		};
		window.open(
			buildUrl( organization, selectedGame, questVersion ),
			'_blank'
		);
	};

	// Render the block.
	return (
		<>
			<InspectorControls>
				<PanelBody title="Settings">
					<div className="jquest-inserter-quest-picker">
						<SelectControl
							label="Select a game"
							value={ selectedGame }
							options={ games }
							onChange={ onChangeGame }
						/>
						<Button
							className="jquest-inserter-refresh-button"
							variant="secondary"
							isBusy={ isRefreshing }
							disabled={ isRefreshing }
							onClick={ refreshGames }
						>
							{ isRefreshing
								? __( 'Refreshing quests…', 'jquest' )
								: __( 'Refresh quests', 'jquest' ) }
						</Button>
					</div>
					{ questVersion !== 'v2' && (
						<SelectControl
							label={ __( 'Script Version', 'jquest' ) }
							value={ version }
							options={ versionOptions }
							onChange={ ( newVersion ) =>
								setAttributes( { version: newVersion } )
							}
							help={ __(
								'Choose which jQuest script version to load.',
								'jquest'
							) }
						/>
					) }
					<ToggleControl
						label={ 'Run as Popup' }
						checked={ !! popup }
						onChange={ ( val ) => setAttributes( { popup: val } ) }
						help={ 'If enabled, the quest will run as a popup.' }
					/>
					{ popup && (
						<>
							<TextControl
								label={ 'Attach popup to' }
								value={ popupAttach }
								onChange={ ( val ) =>
									setAttributes( { popupAttach: val } )
								}
								help={
									"Insert querySelector query for which element to attach popup to (Default 'body')"
								}
							/>
							<ToggleControl
								label={ 'Disable no scroll' }
								checked={ !! popupDisableNoscroll }
								onChange={ ( val ) =>
									setAttributes( {
										popupDisableNoscroll: val,
									} )
								}
								help={
									'If enabled, no scroll effect will not be applied to body when popup open.'
								}
							/>
							<ToggleControl
								label={ 'Disable dismiss' }
								checked={ !! popupDisableDismiss }
								onChange={ ( val ) =>
									setAttributes( {
										popupDisableDismiss: val,
									} )
								}
								help={
									'If enabled, dismiss effect when clicking outside popup will be disabled.'
								}
							/>
							<ToggleControl
								label={ 'Open Automatically' }
								checked={ !! popupAuto }
								onChange={ ( val ) =>
									setAttributes( { popupAuto: val } )
								}
								help={
									'If enabled, the popup will open automatically on page load.'
								}
							/>
							{ ! popupAuto && (
								<>
									<ToggleControl
										label={ 'Add trigger button' }
										checked={ !! popupTriggerButton }
										onChange={ ( val ) =>
											setAttributes( {
												popupTriggerButton: val,
											} )
										}
										help={
											'Trigger button styling comes from global trigger styles set in jQuest settings'
										}
									/>
									{ popupTriggerButton && (
										<>
											<TextControl
												label={ 'Trigger button label' }
												value={
													popupTriggerButtonLabel
												}
												onChange={ ( val ) =>
													setAttributes( {
														popupTriggerButtonLabel:
															val,
													} )
												}
											/>
											<TextControl
												label={
													'Trigger button label mobile'
												}
												value={
													popupTriggerButtonLabelMobile
												}
												onChange={ ( val ) =>
													setAttributes( {
														popupTriggerButtonLabelMobile:
															val,
													} )
												}
											/>
										</>
									) }
								</>
							) }
							{ popupAuto && (
								<>
									<TextControl
										label={ 'Popup Delay (milliseconds)' }
										type="number"
										value={ popupDelay || 0 }
										onChange={ ( val ) =>
											setAttributes( {
												popupDelay: val
													? parseInt( val, 10 )
													: 5000,
											} )
										}
										help={
											'Delay before auto-popup opens.'
										}
										step="1"
										min="0"
									/>
									<TextControl
										label={ 'Popup Limit (times)' }
										type="number"
										value={ popupLimit || 0 }
										onChange={ ( val ) =>
											setAttributes( {
												popupLimit: val
													? parseInt( val, 10 )
													: 0,
											} )
										}
										help={
											"Max times the auto-popup will open. '0' for no limit."
										}
										step="1"
										min="0"
									/>
								</>
							) }
						</>
					) }
				</PanelBody>
			</InspectorControls>
			<div
				{ ...useBlockProps( {
					'data-version': version,
				} ) }
			>
				<div
					className="jquest-app"
					data-org-id={ organization }
					data-game-id={ selectedGame }
					data-version={ questVersion === 'v2' ? 'v2' : undefined }
					data-popup={ popup ? 'true' : 'false' }
					data-popup-auto={ popupAuto ? 'true' : 'false' }
					data-popup-delay={ popupDelay }
					data-popup-limit={ popupLimit }
					data-new-styles="true"
				>
					{ text }
					{ questVersion === 'v2' && (
						<span className="jquest-version-badge">v2</span>
					) }
					{ organization !== '' && selectedGame !== '' && (
						<button onClick={ openDashboard }>
							Edit in dashboard
						</button>
					) }
				</div>
			</div>
		</>
	);
}
