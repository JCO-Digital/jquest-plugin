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
		selectedQuest,
		organization,
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

	// Initialize the state for the quests and text.
	const [ quests, setQuests ] = useState( [] );
	const [ text, setText ] = useState( __( '', 'superquest' ) );
	const [ isRefreshing, setIsRefreshing ] = useState( false );

	/**
	 * Updates the block with a quests API response.
	 *
	 * @param {Object} data Quests API response.
	 */
	const updateQuests = ( data ) => {
		// Set information texts if no quests or organization is found.
		if ( ! data.organization ) {
			setQuests( [] );
			setText(
				__(
					'Organization not set. Set organization in SuperQuest settings',
					'superquest'
				)
			);
			return;
		}

		setAttributes( { organization: data.organization } );

		if ( ! data.quests?.length ) {
			setQuests( [] );
			setText( __( 'No quests found for organization.', 'superquest' ) );
			return;
		}

		// Map the quests to an array of objects with value and label properties.
		const questOptions = data.quests.map( ( quest ) => ( {
			value: quest.id,
			label: quest.title,
			title: quest.title,
		} ) );
		setQuests( questOptions );

		const selectedQuestOption =
			questOptions.find( ( quest ) => quest.value === selectedQuest ) ||
			questOptions[ 0 ];

		if ( selectedQuestOption.value !== selectedQuest ) {
			setAttributes( { selectedQuest: selectedQuestOption.value } );
		}
	};

	// Use the useEffect hook to fetch the quests when the component mounts.
	useEffect( () => {
		apiFetch( {
			path: '/superquest/v1/quests',
		} )
			.then( updateQuests )
			.catch( () => {
				setText( __( 'Unable to load quests.', 'superquest' ) );
			} );
		// eslint-disable-next-line react-hooks/exhaustive-deps
	}, [] );

	/**
	 * Refreshes the organization's quests from the SuperQuest API.
	 */
	const refreshQuests = () => {
		setIsRefreshing( true );
		apiFetch( {
			path: '/superquest/v1/quests/refresh',
			method: 'POST',
		} )
			.then( updateQuests )
			.catch( () => {
				setText( __( 'Unable to refresh quests.', 'superquest' ) );
			} )
			.finally( () => {
				setIsRefreshing( false );
			} );
	};

	// Show the selected quest label in the block.
	useEffect( () => {
		quests.forEach( ( quest ) => {
			if ( quest.value === selectedQuest ) {
				// eslint-disable-next-line @wordpress/i18n-no-variables
				setText( __( quest.title, 'superquest' ) );
			}
		} );
	}, [ selectedQuest, organization, quests ] );

	/**
	 * The `onChangeQuest` function is called when the selected quest changes.
	 * It sets the selected quest attribute to the new quest.
	 *
	 * @param {string} newQuest - The new selected quest.
	 */
	const onChangeQuest = ( newQuest ) => {
		setAttributes( { selectedQuest: newQuest } );
	};

	const openDashboard = () => {
		window.open(
			`https://dashboard.jquest.fi/#/dashboard/${ organization }/quests/${ selectedQuest }`,
			'_blank'
		);
	};

	// Render the block.
	return (
		<>
			<InspectorControls>
				<PanelBody title="Settings">
					<div className="superquest-inserter-quest-picker">
						<SelectControl
							label="Select a quest"
							value={ selectedQuest }
							options={ quests }
							onChange={ onChangeQuest }
						/>
						<Button
							className="superquest-inserter-refresh-button"
							variant="secondary"
							isBusy={ isRefreshing }
							disabled={ isRefreshing }
							onClick={ refreshQuests }
						>
							{ isRefreshing
								? __( 'Refreshing quests…', 'superquest' )
								: __( 'Refresh quests', 'superquest' ) }
						</Button>
					</div>
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
											'Trigger button styling comes from global trigger styles set in SuperQuest settings'
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
			<div { ...useBlockProps() }>
				<div
					className="jquest-app"
					data-org-id={ organization }
					data-game-id={ selectedQuest }
					data-version="v2"
					data-popup={ popup ? 'true' : 'false' }
					data-popup-auto={ popupAuto ? 'true' : 'false' }
					data-popup-delay={ popupDelay }
					data-popup-limit={ popupLimit }
					data-new-styles="true"
				>
					{ text }
					{ organization !== '' && selectedQuest !== '' && (
						<button onClick={ openDashboard }>
							Edit in dashboard
						</button>
					) }
				</div>
			</div>
		</>
	);
}
