/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from "@wordpress/i18n";
import { SelectControl, PanelBody } from "@wordpress/components";
import apiFetch from "@wordpress/api-fetch";
import { useEffect, useState } from "@wordpress/element";

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps, InspectorControls } from "@wordpress/block-editor";

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import "./editor.scss";

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {Element} Element to render.
 */
export default function Edit({ attributes, setAttributes }) {
	const { selectedGame, organization } = attributes;

	// Initialize the state for the games and text.
	const [games, setGames] = useState([]);
	const [text, setText] = useState(__("JQUEST Inserter", "jquest-inserter"));

	// Use the useEffect hook to fetch the games when the component mounts.
	useEffect(() => {
		apiFetch({
			path: "/jquest/v1/games",
		}).then((data) => {
			// Set information texts if no games or organization is found.
			if (!data.organization) {
				setText(
					__(
						"Organization not set. Set organization in JQUEST settings",
						"jquest-inserter",
					),
				);
				return;
			}
			if (!data.games) {
				setText(
					__("No games found for organization", "jquest-inserter"),
				);
				return;
			}

			// Map the games to an array of objects with value and label properties.
			setGames(
				data.games.map((game) => ({
					value: game.id,
					label: game.title,
				})),
			);
			// If there is no selected game, set the selected game to the first game.
			if (!selectedGame) {
				setAttributes({ selectedGame: data.games[0].id });
			}
			// Set the organization attribute.
			setAttributes({ organization: data.organization });
		});
	}, []);

	/**
	 * The `onChangeGame` function is called when the selected game changes.
	 * It sets the selected game attribute to the new game.
	 *
	 * @param {string} newGame - The new selected game.
	 */
	const onChangeGame = (newGame) => {
		setAttributes({ selectedGame: newGame });
	};

	// Render the block.
	return (
		<>
			<InspectorControls>
				<PanelBody title="Settings">
					<SelectControl
						label="Select a game"
						value={selectedGame}
						options={games}
						onChange={onChangeGame}
					/>
				</PanelBody>
			</InspectorControls>
			<div {...useBlockProps()}>
				<div
					className="jquest-app"
					data-org-id={organization}
					data-game-id={selectedGame}
				>
					{text}
				</div>
			</div>
		</>
	);
}
