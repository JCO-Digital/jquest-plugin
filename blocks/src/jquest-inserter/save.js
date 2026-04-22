/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps } from '@wordpress/block-editor';

/**
 * The save function defines the way in which the different attributes should
 * be combined into the final markup, which is then serialized by the block
 * editor into `post_content`.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#save
 *
 * @return {WPElement} Element to render.
 */
export default function save({ attributes }) {
	const {
		selectedGame,
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

	const showTrigger = popup && !popupAuto && popupTriggerButton;

	return (
		<div
			{...useBlockProps.save({
				'data-version': version,
			})}
		>
			<div
				className="jquest-app"
				data-org-id={organization}
				data-game-id={selectedGame}
				data-popup={popup ? 'true' : 'false'}
				data-popup-auto={popupAuto ? 'true' : 'false'}
				data-popup-delay={popupDelay}
				data-popup-limit={popupLimit}
				data-new-styles="true"
				data-popup-disable-dismiss={popupDisableDismiss ? 'true' : 'false'}
				data-popup-disable-noscroll={popupDisableNoscroll ? 'true' : 'false'}
				data-popup-attach={popupAttach ? popupAttach : 'body'}
			></div>
			{showTrigger && (
				<div className="jquest-popup-toggle">
					<a href={`#jquest-popup-${selectedGame}`}>
						{(popupTriggerButtonLabel || popupTriggerButtonLabelMobile) && (
							<span className="label">
								{popupTriggerButtonLabel && (
									<span className="desktop-only">{popupTriggerButtonLabel}</span>
								)}
								{popupTriggerButtonLabelMobile && (
									<span className="mobile-only">
										{popupTriggerButtonLabelMobile}
									</span>
								)}
							</span>
						)}
					</a>
				</div>
			)}
		</div>
	);
}
