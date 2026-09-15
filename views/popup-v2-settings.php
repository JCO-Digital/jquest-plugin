<?php // phpcs:ignore Squiz.Commenting.FileComment.Missing

// $superquest_quests holds the organisation's quests, passed in by the page and
// offered in the selects below. The configured popup entries are their own
// list, kept under its own name so that it does not shadow them.
$superquest_option       = \SuperQuestPlugin\Scripts\POPUP_V2_QUESTS_OPTION;
$superquest_popup_quests = \SuperQuestPlugin\Scripts\popup_v2_quests();

// The form carries no JavaScript, so a blank entry is always appended: filling
// it in and saving adds a quest, and leaving it alone changes nothing because
// the sanitiser drops entries without a quest. It defaults to enabled so that
// adding a quest takes a single save.
$superquest_entries = array_merge(
	$superquest_popup_quests,
	array(
		array(
			'enabled'  => true,
			'quest_id' => '',
		),
	)
);

$superquest_excluded = \SuperQuestPlugin\Scripts\popup_v2_excluded_ids();
?>
<div class="wrap superquest-wrap">
	<div class="superquest-page-header">
		<img class="superquest-logo" src="<?php echo esc_url( \SuperQuestPlugin\logo_url() ); ?>"
			alt="SuperQuest">
		<h1><?php esc_html_e( 'Popup', 'superquest' ); ?></h1>
	</div>

	<div class="superquest-card">
		<form method="post" action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>">
			<?php settings_fields( 'superquest-popup-v2' ); ?>
			<table class="form-table">
				<tr>
					<th scope="row"><?php esc_html_e( 'Loader', 'superquest' ); ?></th>
					<td>
						<input type="hidden" name="<?php echo esc_attr( \SuperQuestPlugin\Scripts\ALWAYS_LOAD_OPTION ); ?>" value="0">
						<label>
							<input type="checkbox"
								name="<?php echo esc_attr( \SuperQuestPlugin\Scripts\ALWAYS_LOAD_OPTION ); ?>"
								value="1"
								<?php checked( get_option( \SuperQuestPlugin\Scripts\ALWAYS_LOAD_OPTION, 0 ), 1 ); ?>>
							<?php esc_html_e( 'Always load the SuperQuest loader', 'superquest' ); ?>
						</label>
						<p class="description"><?php esc_html_e( 'Loads the loader on every page, even ones without a SuperQuest block or popup.', 'superquest' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="<?php echo esc_attr( \SuperQuestPlugin\Scripts\POPUP_V2_EXCLUDE_OPTION ); ?>">
							<?php esc_html_e( 'Excluded pages', 'superquest' ); ?>
						</label>
					</th>
					<td>
						<textarea name="<?php echo esc_attr( \SuperQuestPlugin\Scripts\POPUP_V2_EXCLUDE_OPTION ); ?>"
							id="<?php echo esc_attr( \SuperQuestPlugin\Scripts\POPUP_V2_EXCLUDE_OPTION ); ?>"
							rows="3"
							class="large-text code"
							placeholder="12, 34, 56"><?php echo esc_textarea( implode( ', ', $superquest_excluded ) ); ?></textarea>
						<p class="description">
							<?php esc_html_e( 'Page or post IDs no popup quest is inserted on, separated by commas or line breaks. Applies to every quest.', 'superquest' ); ?>
						</p>
						<?php if ( ! empty( $superquest_excluded ) ) : ?>
							<ul class="superquest-excluded-list">
								<?php
								// The title next to each ID makes a typo, or an ID left
								// behind by a deleted page, obvious at a glance.
								foreach ( $superquest_excluded as $superquest_excluded_id ) :
									$superquest_excluded_title = get_the_title( $superquest_excluded_id );
									?>
									<li>
										<?php echo esc_html( (string) $superquest_excluded_id ); ?> —
										<?php if ( '' !== $superquest_excluded_title ) : ?>
											<?php echo esc_html( $superquest_excluded_title ); ?>
										<?php else : ?>
											<em><?php esc_html_e( 'not found', 'superquest' ); ?></em>
										<?php endif; ?>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</td>
				</tr>
			</table>

			<p class="description">
				<?php esc_html_e( 'Every enabled quest below is inserted at the top of the footer on every page of the site, whatever language it is in, apart from the excluded pages above.', 'superquest' ); ?>
			</p>

			<?php
			foreach ( $superquest_entries as $superquest_index => $superquest_entry ) :
				// Everything past the stored quests is the blank "add" entry.
				$superquest_is_new = $superquest_index >= count( $superquest_popup_quests );
				$superquest_name   = $superquest_option . '[' . $superquest_index . ']';
				$superquest_id     = 'superquest-popup-v2-' . $superquest_index;
				?>
				<fieldset class="superquest-quest-entry<?php echo esc_attr( $superquest_is_new ? ' superquest-quest-entry--new' : '' ); ?>">
					<legend>
						<?php
						echo $superquest_is_new
							? esc_html__( 'Add a quest', 'superquest' )
							/* translators: %d: position of the quest in the list. */
							: esc_html( sprintf( __( 'Quest %d', 'superquest' ), $superquest_index + 1 ) );
						?>
					</legend>

					<table class="form-table">
						<tr>
							<th scope="row"><?php esc_html_e( 'Enabled', 'superquest' ); ?></th>
							<td>
								<?php
								// No hidden companion field here: an unchecked box
								// just leaves the key out of this entry's array,
								// which reads back as disabled.
								?>
								<label>
									<input type="checkbox"
										name="<?php echo esc_attr( $superquest_name . '[enabled]' ); ?>"
										value="1"
										<?php checked( $superquest_entry['enabled'] ); ?>>
									<?php esc_html_e( 'Insert this quest above the footer', 'superquest' ); ?>
								</label>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="<?php echo esc_attr( $superquest_id . '-quest' ); ?>">
									<?php esc_html_e( 'Quest', 'superquest' ); ?>
								</label>
							</th>
							<td>
								<select name="<?php echo esc_attr( $superquest_name . '[quest_id]' ); ?>"
									id="<?php echo esc_attr( $superquest_id . '-quest' ); ?>">
									<option value=""><?php esc_html_e( '— Select a quest —', 'superquest' ); ?></option>
									<?php foreach ( $superquest_quests as $quest ) : ?>
										<option value="<?php echo esc_attr( $quest->id ); ?>"
											<?php selected( $superquest_entry['quest_id'], $quest->id ); ?>>
											<?php echo esc_html( $quest->title ); ?>
										</option>
									<?php endforeach; ?>
								</select>
								<?php if ( ! $superquest_is_new && '' !== $superquest_entry['quest_id'] && ! in_array( $superquest_entry['quest_id'], wp_list_pluck( $superquest_quests, 'id' ), true ) ) : ?>
									<p class="description">
										<?php
										printf(
											/* translators: %s: stored quest ID. */
											esc_html__( 'The saved quest (%s) is no longer in the fetched quest list. Pick another one or remove this entry.', 'superquest' ),
											esc_html( $superquest_entry['quest_id'] )
										);
										?>
									</p>
								<?php endif; ?>
							</td>
						</tr>
						<?php if ( ! $superquest_is_new ) : ?>
							<tr>
								<th scope="row"><?php esc_html_e( 'Remove', 'superquest' ); ?></th>
								<td>
									<label>
										<input type="checkbox"
											name="<?php echo esc_attr( $superquest_name . '[remove]' ); ?>"
											value="1">
										<?php esc_html_e( 'Delete this quest when the settings are saved', 'superquest' ); ?>
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
