function v1Save({ attributes }) {
	const { selectedGame, organization } = attributes;
	return (
		<div {...useBlockProps.save()}>
			<script
				src="https://files.jquest.fi/jquest/jquest.js"
				defer
				async
				type="module"
			></script>
			<div
				className="jquest-app"
				data-org-id={organization}
				data-game-id={selectedGame}
			></div>
		</div>
	);
}

export const v1 = {
	save: v1Save,
};
