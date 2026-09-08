<?php // phpcs:ignore Squiz.Commenting.FileComment.Missing

$jquest_group  = 'jquest-popup-v2-' . $jquest_lang_key;
$jquest_option = \jQuestPlugin\Scripts\popup_v2_quests_option( $jquest_lang_key );
$jquest_quests = \jQuestPlugin\Scripts\popup_v2_quests( $jquest_lang_key );

// The form carries no JavaScript, so a blank entry is always appended: filling
// it in and saving adds a quest, and leaving it alone changes nothing because
// the sanitiser drops entries without a quest. It defaults to enabled so that
// adding a quest takes a single save.
$jquest_entries = array_merge(
	$jquest_quests,
	array(
		array(
			'enabled'  => true,
			'quest_id' => '',
		),
	)
);

// Excluded pages are one site-wide list rather than a per-quest or
// per-language one, so it is read once here for the form above the tabs.
$jquest_excluded = \jQuestPlugin\Scripts\popup_v2_excluded_ids();
?>
<div class="wrap jquest-wrap">
	<div class="jquest-page-header">
		<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 118 137" fill="#1a2e40" aria-hidden="true"><path d="M19.62,45.96v19.05c0,1.2-1.3,1.95-2.34,1.35L.78,56.84c-.48-.28-.78-.79-.78-1.35v-20.85c0-.56.3-1.07.78-1.35L58.07.21c.48-.28,1.08-.28,1.56,0l16.5,9.53c1.04.6,1.04,2.1,0,2.7L20.4,44.61c-.48.28-.78.79-.78,1.35ZM58.15,114.8L2.41,82.62c-1.04-.6-2.34.15-2.34,1.35v19.05c0,.56.29,1.07.78,1.35l57.29,33.08c.48.28,1.08.28,1.56,0l18.06-10.43c.48-.28.78-.79.78-1.35v-19.05c0-1.2-1.3-1.95-2.34-1.35l-16.5,9.53c-.48.28-1.08.28-1.56,0ZM98.08,45.72v64.35c0,1.2,1.3,1.95,2.34,1.35l16.5-9.52c.48-.28.78-.79.78-1.35V34.39c0-.56-.3-1.07-.78-1.35l-18.06-10.43c-.48-.28-1.08-.28-1.56,0l-16.5,9.53c-1.04.6-1.04,2.1,0,2.7l16.5,9.53c.48.28.78.79.78,1.35ZM77.94,80.54c.38-.3.61-.75.61-1.24v-20.94c0-.49-.23-.94-.61-1.24l-18.31-10.58c-.49-.28-1.08-.28-1.56,0l-18.31,10.58c-.38.3-.61.75-.61,1.24v20.94c0,.49.23.94.61,1.24l18.31,10.58c.47.28,1.07.28,1.56,0l18.31-10.58Z"/></svg>
		<h1><?php esc_html_e( 'jQuest Popup v2', 'jquest' ); ?></h1>
	</div>

	<div class="jquest-card">
		<form method="post" action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>">
			<?php settings_fields( 'jquest-popup-v2-global' ); ?>
			<table class="form-table">
				<tr>
					<th scope="row"><?php esc_html_e( 'Loader', 'jquest' ); ?></th>
					<td>
						<input type="hidden" name="<?php echo esc_attr( \jQuestPlugin\Scripts\ALWAYS_LOAD_OPTION ); ?>" value="0">
						<label>
							<input type="checkbox"
								name="<?php echo esc_attr( \jQuestPlugin\Scripts\ALWAYS_LOAD_OPTION ); ?>"
								value="1"
								<?php checked( get_option( \jQuestPlugin\Scripts\ALWAYS_LOAD_OPTION, 0 ), 1 ); ?>>
							<?php esc_html_e( 'Always load the jQuest loader', 'jquest' ); ?>
						</label>
						<p class="description"><?php esc_html_e( 'Loads the loader on every page, even ones without a jQuest block or popup. Applies to all languages.', 'jquest' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="<?php echo esc_attr( \jQuestPlugin\Scripts\POPUP_V2_EXCLUDE_OPTION ); ?>">
							<?php esc_html_e( 'Excluded pages', 'jquest' ); ?>
						</label>
					</th>
					<td>
						<textarea name="<?php echo esc_attr( \jQuestPlugin\Scripts\POPUP_V2_EXCLUDE_OPTION ); ?>"
							id="<?php echo esc_attr( \jQuestPlugin\Scripts\POPUP_V2_EXCLUDE_OPTION ); ?>"
							rows="3"
							class="large-text code"
							placeholder="12, 34, 56"><?php echo esc_textarea( implode( ', ', $jquest_excluded ) ); ?></textarea>
						<p class="description">
							<?php esc_html_e( 'Page or post IDs no Popup v2 quest is inserted on, separated by commas or line breaks. Applies to every quest and every language. Use this for pages that embed a jQuest block on the stable or latest script — only one script can run per page, so a v2 popup would otherwise force the whole page onto v2 and break the block.', 'jquest' ); ?>
						</p>
						<?php if ( ! empty( $jquest_excluded ) ) : ?>
							<ul class="jquest-excluded-list">
								<?php
								// The title next to each ID makes a typo, or an ID left
								// behind by a deleted page, obvious at a glance.
								foreach ( $jquest_excluded as $jquest_excluded_id ) :
									$jquest_excluded_title = get_the_title( $jquest_excluded_id );
									?>
									<li>
										<?php echo esc_html( (string) $jquest_excluded_id ); ?> —
										<?php if ( '' !== $jquest_excluded_title ) : ?>
											<?php echo esc_html( $jquest_excluded_title ); ?>
										<?php else : ?>
											<em><?php esc_html_e( 'not found', 'jquest' ); ?></em>
										<?php endif; ?>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>

	<?php if ( ! empty( $jquest_tabs ) ) : ?>
		<div class="nav-tab-wrapper">
			<?php foreach ( $jquest_tabs as $jquest_tab => $tab_data ) : ?>
				<a href="<?php echo esc_url( $tab_data['url'] ); ?>"
					class="nav-tab
					<?php
					echo $jquest_active_tab === $jquest_tab
					? 'nav-tab-active'
					: '';
					?>
		"><?php echo esc_html( $tab_data['label'] ); ?></a>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<div class="jquest-card">
		<form method="post" action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>">
			<?php settings_fields( $jquest_group ); ?>

			<p class="description">
				<?php esc_html_e( 'Every enabled quest below is inserted at the top of the footer on every page of this language, apart from the excluded pages above. Only v2 quests can be used here.', 'jquest' ); ?>
			</p>

			<?php
			foreach ( $jquest_entries as $jquest_index => $jquest_entry ) :
				// Everything past the stored quests is the blank "add" entry.
				$jquest_is_new = $jquest_index >= count( $jquest_quests );
				$jquest_name   = $jquest_option . '[' . $jquest_index . ']';
				$jquest_id     = 'jquest-popup-v2-' . $jquest_lang_key . '-' . $jquest_index;
				?>
				<fieldset class="jquest-quest-entry<?php echo esc_attr( $jquest_is_new ? ' jquest-quest-entry--new' : '' ); ?>">
					<legend>
						<?php
						echo $jquest_is_new
							? esc_html__( 'Add a quest', 'jquest' )
							/* translators: %d: position of the quest in the list. */
							: esc_html( sprintf( __( 'Quest %d', 'jquest' ), $jquest_index + 1 ) );
						?>
					</legend>

					<table class="form-table">
						<tr>
							<th scope="row"><?php esc_html_e( 'Enabled', 'jquest' ); ?></th>
							<td>
								<?php
								// No hidden companion field here: an unchecked box
								// just leaves the key out of this entry's array,
								// which reads back as disabled.
								?>
								<label>
									<input type="checkbox"
										name="<?php echo esc_attr( $jquest_name . '[enabled]' ); ?>"
										value="1"
										<?php checked( $jquest_entry['enabled'] ); ?>>
									<?php esc_html_e( 'Insert this quest above the footer', 'jquest' ); ?>
								</label>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="<?php echo esc_attr( $jquest_id . '-quest' ); ?>">
									<?php esc_html_e( 'Quest', 'jquest' ); ?>
								</label>
							</th>
							<td>
								<select name="<?php echo esc_attr( $jquest_name . '[quest_id]' ); ?>"
									id="<?php echo esc_attr( $jquest_id . '-quest' ); ?>">
									<option value=""><?php esc_html_e( '— Select a quest —', 'jquest' ); ?></option>
									<?php foreach ( $jquest_games as $game ) : ?>
										<option value="<?php echo esc_attr( $game->id ); ?>"
											<?php selected( $jquest_entry['quest_id'], $game->id ); ?>>
											<?php echo esc_html( $game->title ); ?>
										</option>
									<?php endforeach; ?>
								</select>
								<?php if ( ! $jquest_is_new && '' !== $jquest_entry['quest_id'] && ! in_array( $jquest_entry['quest_id'], wp_list_pluck( $jquest_games, 'id' ), true ) ) : ?>
									<p class="description">
										<?php
										printf(
											/* translators: %s: stored quest ID. */
											esc_html__( 'The saved quest (%s) is no longer in the fetched quest list. Pick another one or remove this entry.', 'jquest' ),
											esc_html( $jquest_entry['quest_id'] )
										);
										?>
									</p>
								<?php endif; ?>
							</td>
						</tr>
						<?php if ( ! $jquest_is_new ) : ?>
							<tr>
								<th scope="row"><?php esc_html_e( 'Remove', 'jquest' ); ?></th>
								<td>
									<label>
										<input type="checkbox"
											name="<?php echo esc_attr( $jquest_name . '[remove]' ); ?>"
											value="1">
										<?php esc_html_e( 'Delete this quest when the settings are saved', 'jquest' ); ?>
									</label>
								</td>
							</tr>
						<?php endif; ?>
					</table>
				</fieldset>
			<?php endforeach; ?>

			<?php submit_button(); ?>
		</form>
	</div>
</div>
