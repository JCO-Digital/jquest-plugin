/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from "@wordpress/i18n";
import {
  SelectControl,
  PanelBody,
  ToggleControl,
  TextControl,
} from "@wordpress/components";
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
 * @return {JSX.Element} Element to render.
 */
export default function Edit({ attributes, setAttributes }) {
  const {
    selectedGame,
    organization,
    version,
    popup,
    popupAuto,
    popupDelay,
    popupLimit,
  } = attributes;

  // Initialize the state for the games and text.
  const [games, setGames] = useState([]);
  const [text, setText] = useState(__("", "jquest-inserter"));

  const versions = ["stable", "latest"];

  const versionOptions = versions.map((version) => ({
    label: version.charAt(0).toUpperCase() + version.slice(1),
    value: version,
  }));

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
        setText(__("No games found for organization.", "jquest-inserter"));
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

  // Show the selected game label in the block.
  useEffect(() => {
    games.find((game) => {
      if (game.value === selectedGame) {
        setText(__(game.label, "jquest-inserter"));
      }
    });
  }, [selectedGame, organization, games]);

  /**
   * The `onChangeGame` function is called when the selected game changes.
   * It sets the selected game attribute to the new game.
   *
   * @param {string} newGame - The new selected game.
   */
  const onChangeGame = (newGame) => {
    setAttributes({ selectedGame: newGame });
  };

  const openDashboard = (state) => {
    // go to dashboard
    window.open(
      "https://dashboard.jquest.fi/#/dashboard/" +
        organization +
        "/" +
        selectedGame,
      "_blank",
    );
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
          <SelectControl
            label={__("Script Version", "jquest-inserter")}
            value={version}
            options={versionOptions}
            onChange={(newVersion) => setAttributes({ version: newVersion })}
            help={__(
              "Choose which jQuest script version to load.",
              "jquest-inserter",
            )}
          />
          <ToggleControl
            label={"Run as Popup"}
            checked={!!popup}
            onChange={(val) => setAttributes({ popup: val })}
            help={"If enabled, the quest will run as a popup."}
          />
          {popup && (
            <>
              <ToggleControl
                label={"Open Automatically"}
                checked={!!popupAuto}
                onChange={(val) => setAttributes({ popupAuto: val })}
                help={
                  "If enabled, the popup will open automatically on page load."
                }
              />
              {popupAuto && (
                <>
                  <TextControl
                    label={"Popup Delay (milliseconds)"}
                    type="number"
                    value={popupDelay || 0}
                    onChange={(val) =>
                      setAttributes({
                        popupDelay: val ? parseInt(val, 10) : 5000,
                      })
                    }
                    help={"Delay before auto-popup opens."}
                    step="1"
                    min="0"
                  />
                  <TextControl
                    label={"Popup Limit (times)"}
                    type="number"
                    value={popupLimit || 0}
                    onChange={(val) =>
                      setAttributes({
                        popupLimit: val ? parseInt(val, 10) : 0,
                      })
                    }
                    help={
                      "Max times the auto-popup will open. '0' for no limit."
                    }
                    step="1"
                    min="0"
                  />
                </>
              )}
            </>
          )}
        </PanelBody>
      </InspectorControls>
      <div
        {...useBlockProps({
          "data-version": version,
        })}
      >
        <div
          className="jquest-app"
          data-org-id={organization}
          data-game-id={selectedGame}
          data-popup={popup ? "true" : "false"}
          data-popup-auto={popupAuto ? "true" : "false"}
          data-popup-delay={popupDelay}
          data-popup-limit={popupLimit}
          data-new-styles="true"
        >
          {text}
          {organization !== "" && selectedGame !== "" && (
            <button onClick={openDashboard}>Edit in dashboard</button>
          )}
        </div>
      </div>
    </>
  );
}
