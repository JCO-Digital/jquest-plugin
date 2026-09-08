<?php // phpcs:ignore Squiz.Commenting.FileComment.Missing

$jquest_prefix = \jQuestPlugin\Scripts\popup_v2_prefix( $jquest_lang_key );
$jquest_group  = 'jquest-popup-v2-' . $jquest_lang_key;
?>
<div class="wrap jquest-wrap">
	<div class="jquest-page-header">
		<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 118 137" fill="#1a2e40" aria-hidden="true"><path d="M19.62,45.96v19.05c0,1.2-1.3,1.95-2.34,1.35L.78,56.84c-.48-.28-.78-.79-.78-1.35v-20.85c0-.56.3-1.07.78-1.35L58.07.21c.48-.28,1.08-.28,1.56,0l16.5,9.53c1.04.6,1.04,2.1,0,2.7L20.4,44.61c-.48.28-.78.79-.78,1.35ZM58.15,114.8L2.41,82.62c-1.04-.6-2.34.15-2.34,1.35v19.05c0,.56.29,1.07.78,1.35l57.29,33.08c.48.28,1.08.28,1.56,0l18.06-10.43c.48-.28.78-.79.78-1.35v-19.05c0-1.2-1.3-1.95-2.34-1.35l-16.5,9.53c-.48.28-1.08.28-1.56,0ZM98.08,45.72v64.35c0,1.2,1.3,1.95,2.34,1.35l16.5-9.52c.48-.28.78-.79.78-1.35V34.39c0-.56-.3-1.07-.78-1.35l-18.06-10.43c-.48-.28-1.08-.28-1.56,0l-16.5,9.53c-1.04.6-1.04,2.1,0,2.7l16.5,9.53c.48.28.78.79.78,1.35ZM77.94,80.54c.38-.3.61-.75.61-1.24v-20.94c0-.49-.23-.94-.61-1.24l-18.31-10.58c-.49-.28-1.08-.28-1.56,0l-18.31,10.58c-.38.3-.61.75-.61,1.24v20.94c0,.49.23.94.61,1.24l18.31,10.58c.47.28,1.07.28,1.56,0l18.31-10.58Z"/></svg>
		<h1><?php esc_html_e( 'jQuest Popup v2', 'jquest' ); ?></h1>
	</div>

	<div class="jquest-card">
		<form method="post" action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>">
			<?php settings_fields( 'jquest-loader' ); ?>
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

			<table class="form-table">
				<tr>
					<th scope="row"><?php esc_html_e( 'Enabled', 'jquest' ); ?></th>
					<td>
						<input type="hidden" name="<?php echo esc_attr( $jquest_prefix . 'enabled' ); ?>" value="0">
						<label>
							<input type="checkbox"
								name="<?php echo esc_attr( $jquest_prefix . 'enabled' ); ?>"
								value="1"
								<?php checked( get_option( $jquest_prefix . 'enabled', 0 ), 1 ); ?>>
							<?php esc_html_e( 'Insert quest above the footer', 'jquest' ); ?>
						</label>
						<p class="description"><?php esc_html_e( 'Outputs the selected quest at the top of the footer on every page of this language.', 'jquest' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="<?php echo esc_attr( $jquest_prefix . 'quest_id' ); ?>">
							<?php esc_html_e( 'Quest', 'jquest' ); ?>
						</label>
					</th>
					<td>
						<?php $jquest_selected_quest_id = get_option( $jquest_prefix . 'quest_id', '' ); ?>
						<select name="<?php echo esc_attr( $jquest_prefix . 'quest_id' ); ?>"
							id="<?php echo esc_attr( $jquest_prefix . 'quest_id' ); ?>">
							<option value=""><?php esc_html_e( '— Select a quest —', 'jquest' ); ?></option>
							<?php foreach ( $jquest_games as $game ) : ?>
								<option value="<?php echo esc_attr( $game->id ); ?>"
									<?php selected( $jquest_selected_quest_id, $game->id ); ?>>
									<?php echo esc_html( $game->title ); ?>
								</option>
							<?php endforeach; ?>
						</select>
						<p class="description"><?php esc_html_e( 'Only v2 quests can be used here.', 'jquest' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="<?php echo esc_attr( $jquest_prefix . 'exclude_ids' ); ?>">
							<?php esc_html_e( 'Excluded pages', 'jquest' ); ?>
						</label>
					</th>
					<td>
						<?php $jquest_excluded = \jQuestPlugin\Scripts\popup_v2_excluded_ids( $jquest_lang_key ); ?>
						<textarea name="<?php echo esc_attr( $jquest_prefix . 'exclude_ids' ); ?>"
							id="<?php echo esc_attr( $jquest_prefix . 'exclude_ids' ); ?>"
							rows="3"
							class="large-text code"
							placeholder="12, 34, 56"><?php echo esc_textarea( implode( ', ', $jquest_excluded ) ); ?></textarea>
						<p class="description">
							<?php esc_html_e( 'Page or post IDs this popup is left off, separated by commas or line breaks. Use this for pages that embed a jQuest block on the stable or latest script — only one script can run per page, so a v2 popup would otherwise force the whole page onto v2 and break the block.', 'jquest' ); ?>
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
</div>
