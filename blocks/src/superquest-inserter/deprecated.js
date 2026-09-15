/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps } from '@wordpress/block-editor';

/**
 * Popup behaviour is configured on the plugin's popup settings page rather than
 * per block, so the block no longer registers these attributes. Every
 * deprecation below still declares them because the markup it parses was saved
 * with them, and every `migrate` drops them again on the way out.
 */
const popupAttributes = {
	popup: { type: 'boolean', default: false },
	popupAuto: { type: 'boolean', default: false },
	popupDisableDismiss: { type: 'boolean', default: false },
	popupDisableNoscroll: { type: 'boolean', default: false },
	popupAttach: { type: 'string', default: 'body' },
	popupDelay: { type: 'number', default: 5000 },
	popupLimit: { type: 'number', default: 0 },
	popupTriggerButton: { type: 'boolean', default: false },
	popupTriggerButtonLabel: { type: 'string', default: '' },
	popupTriggerButtonLabelMobile: { type: 'string', default: '' },
};

/**
 * Drops the retired popup attributes from a parsed block.
 *
 * @param {Object} attributes Attributes parsed by a deprecation.
 * @return {Object} Attributes without the popup settings.
 */
const withoutPopup = ( attributes ) => {
	const {
		popup,
		popupAuto,
		popupDisableDismiss,
		popupDisableNoscroll,
		popupAttach,
		popupDelay,
		popupLimit,
		popupTriggerButton,
		popupTriggerButtonLabel,
		popupTriggerButtonLabelMobile,
		...rest
	} = attributes;
	return rest;
};

/**
 * The last markup written before the popup settings left the block: the quest
 * container carries the popup data attributes the loader used to read, and a
 * standalone trigger button follows it when one was configured. Both are gone
 * now, so this deprecation parses that markup and keeps only the quest.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-deprecation/
 */
const v3 = {
	attributes: {
		selectedQuest: { type: 'string' },
		organization: { type: 'string' },
		...popupAttributes,
	},
	supports: {
		html: false,
	},
	migrate: withoutPopup,
	save( { attributes } ) {
		const {
			selectedQuest,
			organization,
			popup,
			popupAuto,
			popupDelay,
			popupLimit,
			popupDisableNoscroll,
			popupDisableDismiss,
			popupAttach,
			popupTriggerButton,
			popupTriggerButtonLabel,
			popupTriggerButtonLabelMobile,
		} = attributes;

		const showTrigger = popup && ! popupAuto && popupTriggerButton;

		return (
			<div { ...useBlockProps.save() }>
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
					data-popup-disable-dismiss={
						popupDisableDismiss ? 'true' : 'false'
					}
					data-popup-disable-noscroll={
						popupDisableNoscroll ? 'true' : 'false'
					}
					data-popup-attach={ popupAttach ? popupAttach : 'body' }
				></div>
				{ showTrigger && (
					<div className="jquest-popup-toggle" data-jq-load="hover">
						<a href={ `#jquest-popup-${ selectedQuest }` }>
							{ ( popupTriggerButtonLabel ||
								popupTriggerButtonLabelMobile ) && (
								<span className="label">
									{ popupTriggerButtonLabel && (
										<span className="desktop-only">
											{ popupTriggerButtonLabel }
										</span>
									) }
									{ popupTriggerButtonLabelMobile && (
										<span className="mobile-only">
											{ popupTriggerButtonLabelMobile }
										</span>
									) }
								</span>
							) }
						</a>
					</div>
				) }
			</div>
		);
	},
};

/**
 * The quest a block points at was called `selectedGame` before the plugin
 * settled on quests throughout. The markup is otherwise the same as v3 —
 * `data-game-id` is the external loader's attribute name and stays — so this
 * deprecation mostly exists to read the old attribute and hand it back under
 * the new name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-deprecation/
 */
const v2 = {
	attributes: {
		selectedGame: { type: 'string' },
		organization: { type: 'string' },
		...popupAttributes,
	},
	supports: {
		html: false,
	},
	migrate( attributes ) {
		const { selectedGame, ...rest } = withoutPopup( attributes );
		return { ...rest, selectedQuest: selectedGame };
	},
	save( { attributes } ) {
		const {
			selectedGame,
			organization,
			popup,
			popupAuto,
			popupDelay,
			popupLimit,
			popupDisableNoscroll,
			popupDisableDismiss,
			popupAttach,
			popupTriggerButton,
			popupTriggerButtonLabel,
			popupTriggerButtonLabelMobile,
		} = attributes;

		const showTrigger = popup && ! popupAuto && popupTriggerButton;

		return (
			<div { ...useBlockProps.save() }>
				<div
					className="jquest-app"
					data-org-id={ organization }
					data-game-id={ selectedGame }
					data-version="v2"
					data-popup={ popup ? 'true' : 'false' }
					data-popup-auto={ popupAuto ? 'true' : 'false' }
					data-popup-delay={ popupDelay }
					data-popup-limit={ popupLimit }
					data-new-styles="true"
					data-popup-disable-dismiss={
						popupDisableDismiss ? 'true' : 'false'
					}
					data-popup-disable-noscroll={
						popupDisableNoscroll ? 'true' : 'false'
					}
					data-popup-attach={ popupAttach ? popupAttach : 'body' }
				></div>
				{ showTrigger && (
					<div className="jquest-popup-toggle" data-jq-load="hover">
						<a href={ `#jquest-popup-${ selectedGame }` }>
							{ ( popupTriggerButtonLabel ||
								popupTriggerButtonLabelMobile ) && (
								<span className="label">
									{ popupTriggerButtonLabel && (
										<span className="desktop-only">
											{ popupTriggerButtonLabel }
										</span>
									) }
									{ popupTriggerButtonLabelMobile && (
										<span className="mobile-only">
											{ popupTriggerButtonLabelMobile }
										</span>
									) }
								</span>
							) }
						</a>
					</div>
				) }
			</div>
		);
	},
};

/**
 * Content saved before the script channels were removed carries a `version`
 * attribute naming the bundle to load and a `questVersion` naming the quest
 * generation, and its markup reflects both. Only one script exists now, so this
 * deprecation parses that older markup and drops the two attributes instead of
 * flagging every existing block as invalid. It also predates the quest rename,
 * so it carries `selectedGame` over the same way v2 does.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-deprecation/
 */
const v1 = {
	attributes: {
		selectedGame: { type: 'string' },
		questVersion: { type: 'string', default: 'v1' },
		organization: { type: 'string' },
		version: { type: 'string', default: 'stable' },
		...popupAttributes,
	},
	supports: {
		html: false,
	},
	migrate( attributes ) {
		const { version, questVersion, selectedGame, ...rest } =
			withoutPopup( attributes );
		return { ...rest, selectedQuest: selectedGame };
	},
	save( { attributes } ) {
		const {
			selectedGame,
			questVersion,
			organization,
			popup,
			popupAuto,
			popupDelay,
			popupLimit,
			version,
			popupDisableNoscroll,
			popupDisableDismiss,
			popupAttach,
			popupTriggerButton,
			popupTriggerButtonLabel,
			popupTriggerButtonLabelMobile,
		} = attributes;

		const showTrigger = popup && ! popupAuto && popupTriggerButton;

		return (
			<div
				{ ...useBlockProps.save( {
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
					data-popup-disable-dismiss={
						popupDisableDismiss ? 'true' : 'false'
					}
					data-popup-disable-noscroll={
						popupDisableNoscroll ? 'true' : 'false'
					}
					data-popup-attach={ popupAttach ? popupAttach : 'body' }
				></div>
				{ showTrigger && (
					<div className="jquest-popup-toggle" data-jq-load="hover">
						<a href={ `#jquest-popup-${ selectedGame }` }>
							{ ( popupTriggerButtonLabel ||
								popupTriggerButtonLabelMobile ) && (
								<span className="label">
									{ popupTriggerButtonLabel && (
										<span className="desktop-only">
											{ popupTriggerButtonLabel }
										</span>
									) }
									{ popupTriggerButtonLabelMobile && (
										<span className="mobile-only">
											{ popupTriggerButtonLabelMobile }
										</span>
									) }
								</span>
							) }
						</a>
					</div>
				) }
			</div>
		);
	},
};

export default [ v3, v2, v1 ];
