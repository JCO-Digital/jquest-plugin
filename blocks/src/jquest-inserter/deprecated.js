/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps } from '@wordpress/block-editor';

/**
 * Content saved before the script channels were removed carries a `version`
 * attribute naming the bundle to load and a `questVersion` naming the quest
 * generation, and its markup reflects both. Only one script exists now, so this
 * deprecation parses that older markup and drops the two attributes instead of
 * flagging every existing block as invalid.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-deprecation/
 */
const v1 = {
	attributes: {
		selectedGame: { type: 'string' },
		questVersion: { type: 'string', default: 'v1' },
		organization: { type: 'string' },
		version: { type: 'string', default: 'stable' },
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
	},
	supports: {
		html: false,
	},
	migrate( attributes ) {
		const { version, questVersion, ...rest } = attributes;
		return rest;
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

export default [ v1 ];
