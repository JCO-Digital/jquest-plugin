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
 * @param {Object} root0            Component props.
 * @param {Object} root0.attributes Block attributes.
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#save
 *
 * @return {import('@wordpress/element').WPElement} Element to render.
 */
export default function save( { attributes } ) {
	const { selectedQuest, organization } = attributes;

	return (
		<div { ...useBlockProps.save() }>
			<div
				className="jquest-app"
				data-org-id={ organization }
				data-game-id={ selectedQuest }
				data-version="v2"
				data-new-styles="true"
			></div>
		</div>
	);
}
