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
		<img class="jquest-logo" src="<?php echo esc_url( \jQuestPlugin\logo_url() ); ?>"
			alt="SuperQuest">
		<h1><?php esc_html_e( 'Popup', 'jquest' ); ?></h1>
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
							<?php esc_html_e( 'Always load the SuperQuest loader', 'jquest' ); ?>
						</label>
						<p class="description"><?php esc_html_e( 'Loads the loader on every page, even ones without a SuperQuest block or popup. Applies to all languages.', 'jquest' ); ?></p>
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
							<?php esc_html_e( 'Page or post IDs no popup quest is inserted on, separated by commas or line breaks. Applies to every quest and every language.', 'jquest' ); ?>
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
				<?php esc_html_e( 'Every enabled quest below is inserted at the top of the footer on every page of this language, apart from the excluded pages above.', 'jquest' ); ?>
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
