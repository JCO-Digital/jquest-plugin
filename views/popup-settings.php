<?php // phpcs:ignore Squiz.Commenting.FileComment.Missing

$jquest_prefix = 'jquest_popup_' . $jquest_lang_key . '_';
$jquest_group  = 'jquest-popup-' . $jquest_lang_key;

?>
<div class="wrap jquest-wrap">
	<div class="jquest-page-header">
		<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 118 137" fill="#1a2e40" aria-hidden="true"><path d="M19.62,45.96v19.05c0,1.2-1.3,1.95-2.34,1.35L.78,56.84c-.48-.28-.78-.79-.78-1.35v-20.85c0-.56.3-1.07.78-1.35L58.07.21c.48-.28,1.08-.28,1.56,0l16.5,9.53c1.04.6,1.04,2.1,0,2.7L20.4,44.61c-.48.28-.78.79-.78,1.35ZM58.15,114.8L2.41,82.62c-1.04-.6-2.34.15-2.34,1.35v19.05c0,.56.29,1.07.78,1.35l57.29,33.08c.48.28,1.08.28,1.56,0l18.06-10.43c.48-.28.78-.79.78-1.35v-19.05c0-1.2-1.3-1.95-2.34-1.35l-16.5,9.53c-.48.28-1.08.28-1.56,0ZM98.08,45.72v64.35c0,1.2,1.3,1.95,2.34,1.35l16.5-9.52c.48-.28.78-.79.78-1.35V34.39c0-.56-.3-1.07-.78-1.35l-18.06-10.43c-.48-.28-1.08-.28-1.56,0l-16.5,9.53c-1.04.6-1.04,2.1,0,2.7l16.5,9.53c.48.28.78.79.78,1.35ZM77.94,80.54c.38-.3.61-.75.61-1.24v-20.94c0-.49-.23-.94-.61-1.24l-18.31-10.58c-.49-.28-1.08-.28-1.56,0l-18.31,10.58c-.38.3-.61.75-.61,1.24v20.94c0,.49.23.94.61,1.24l18.31,10.58c.47.28,1.07.28,1.56,0l18.31-10.58Z"/></svg>
		<h1><?php esc_html_e( 'jQuest Popup', 'jquest-' ); ?></h1>
	</div>

	<?php if ( ! empty( $jquest_tabs ) ) : ?>
		<div class="nav-tab-wrapper">
			<?php foreach ( $jquest_tabs as $jquest_tab => $tab_data ) : ?>
				<a href="<?php echo esc_url( $tab_data['url'] ); ?>"
					class="nav-tab <?php echo $jquest_active_tab === $jquest_tab ? 'nav-tab-active' : ''; ?>"><?php echo esc_html( $tab_data['label'] ); ?></a>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<?php if ( 'trigger' === $jquest_lang_key ) : ?>

	<div class="jquest-card">
	<form method="post" action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>">
		<?php settings_fields( 'jquest-popup-trigger' ); ?>

		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e( 'Enabled', 'jquest-' ); ?></th>
				<td>
					<input type="hidden" name="jquest_popup_trigger_enabled" value="0">
					<label>
						<input type="checkbox"
							name="jquest_popup_trigger_enabled"
							value="1"
							<?php checked( get_option( 'jquest_popup_trigger_enabled', 0 ), 1 ); ?>>
						<?php esc_html_e( 'Enable trigger button', 'jquest-' ); ?>
					</label>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="jquest_popup_trigger_text_color">
						<?php esc_html_e( 'Text colour', 'jquest-' ); ?>
					</label>
				</th>
				<td>
					<input type="text"
						name="jquest_popup_trigger_text_color"
						id="jquest_popup_trigger_text_color"
						class="jquest-color-picker"
						value="<?php echo esc_attr( get_option( 'jquest_popup_trigger_text_color', '#1a2e40' ) ); ?>">
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="jquest_popup_trigger_bg_color">
						<?php esc_html_e( 'Background colour', 'jquest-' ); ?>
					</label>
				</th>
				<td>
					<input type="text"
						name="jquest_popup_trigger_bg_color"
						id="jquest_popup_trigger_bg_color"
						class="jquest-color-picker"
						value="<?php echo esc_attr( get_option( 'jquest_popup_trigger_bg_color', '#ffffff' ) ); ?>">
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="jquest_popup_trigger_text_hover_color">
						<?php esc_html_e( 'Text hover colour', 'jquest-' ); ?>
					</label>
				</th>
				<td>
					<input type="text"
						name="jquest_popup_trigger_text_hover_color"
						id="jquest_popup_trigger_text_hover_color"
						class="jquest-color-picker"
						value="<?php echo esc_attr( get_option( 'jquest_popup_trigger_text_hover_color', '#1a2e40' ) ); ?>">
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="jquest_popup_trigger_bg_hover_color">
						<?php esc_html_e( 'Background hover colour', 'jquest-' ); ?>
					</label>
				</th>
				<td>
					<input type="text"
						name="jquest_popup_trigger_bg_hover_color"
						id="jquest_popup_trigger_bg_hover_color"
						class="jquest-color-picker"
						value="<?php echo esc_attr( get_option( 'jquest_popup_trigger_bg_hover_color', '#f0f0f0' ) ); ?>">
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="jquest_popup_trigger_icon_bg_color">
						<?php esc_html_e( 'Icon container background', 'jquest-' ); ?>
					</label>
				</th>
				<td>
					<input type="text"
						name="jquest_popup_trigger_icon_bg_color"
						id="jquest_popup_trigger_icon_bg_color"
						class="jquest-color-picker"
						value="<?php echo esc_attr( get_option( 'jquest_popup_trigger_icon_bg_color', '' ) ); ?>">
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="jquest_popup_trigger_icon_bg_hover_color">
						<?php esc_html_e( 'Icon container background hover', 'jquest-' ); ?>
					</label>
				</th>
				<td>
					<input type="text"
						name="jquest_popup_trigger_icon_bg_hover_color"
						id="jquest_popup_trigger_icon_bg_hover_color"
						class="jquest-color-picker"
						value="<?php echo esc_attr( get_option( 'jquest_popup_trigger_icon_bg_hover_color', '' ) ); ?>">
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="jquest_popup_trigger_icon_mode">
						<?php esc_html_e( 'Icon', 'jquest-' ); ?>
					</label>
				</th>
				<td>
					<select name="jquest_popup_trigger_icon_mode" id="jquest_popup_trigger_icon_mode">
						<option value="default" <?php selected( get_option( 'jquest_popup_trigger_icon_mode', 'default' ), 'default' ); ?>>
							<?php esc_html_e( 'Default', 'jquest-' ); ?>
						</option>
						<option value="none" <?php selected( get_option( 'jquest_popup_trigger_icon_mode', 'default' ), 'none' ); ?>>
							<?php esc_html_e( 'None', 'jquest-' ); ?>
						</option>
						<option value="custom" <?php selected( get_option( 'jquest_popup_trigger_icon_mode', 'default' ), 'custom' ); ?>>
							<?php esc_html_e( 'Custom', 'jquest-' ); ?>
						</option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="jquest_popup_trigger_icon_custom">
						<?php esc_html_e( 'Custom icon SVG', 'jquest-' ); ?>
					</label>
				</th>
				<td>
					<textarea
						name="jquest_popup_trigger_icon_custom"
						id="jquest_popup_trigger_icon_custom"
						rows="5"
						class="large-text code"><?php echo esc_textarea( get_option( 'jquest_popup_trigger_icon_custom', '' ) ); ?></textarea>
				</td>
			</tr>
		</table>

		<?php submit_button(); ?>
	</form>
	</div>

	<?php else : ?>

	<div class="jquest-card">
	<form method="post" action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>">
		<?php settings_fields( $jquest_group ); ?>

		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e( 'Enabled', 'jquest-' ); ?></th>
				<td>
					<input type="hidden" name="<?php echo esc_attr( $jquest_prefix . 'enabled' ); ?>" value="0">
					<label>
						<input type="checkbox"
							name="<?php echo esc_attr( $jquest_prefix . 'enabled' ); ?>"
							value="1"
							<?php checked( get_option( $jquest_prefix . 'enabled', 0 ), 1 ); ?>>
						<?php esc_html_e( 'Enable popup', 'jquest-' ); ?>
					</label>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="<?php echo esc_attr( $jquest_prefix . 'quest_id' ); ?>">
						<?php esc_html_e( 'Quest', 'jquest-' ); ?>
					</label>
				</th>
				<td>
					<select name="<?php echo esc_attr( $jquest_prefix . 'quest_id' ); ?>"
						id="<?php echo esc_attr( $jquest_prefix . 'quest_id' ); ?>">
						<option value=""><?php esc_html_e( '— Select a quest —', 'jquest-' ); ?></option>
						<?php foreach ( $jquest_games as $game ) : ?>
							<option value="<?php echo esc_attr( $game->id ); ?>"
								<?php selected( get_option( $jquest_prefix . 'quest_id', '' ), $game->id ); ?>>
								<?php echo esc_html( $game->title ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="<?php echo esc_attr( $jquest_prefix . 'desktop_label' ); ?>">
						<?php esc_html_e( 'Desktop label', 'jquest-' ); ?>
					</label>
				</th>
				<td>
					<input type="text"
						name="<?php echo esc_attr( $jquest_prefix . 'desktop_label' ); ?>"
						id="<?php echo esc_attr( $jquest_prefix . 'desktop_label' ); ?>"
						value="<?php echo esc_attr( get_option( $jquest_prefix . 'desktop_label', '' ) ); ?>"
						class="regular-text">
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="<?php echo esc_attr( $jquest_prefix . 'mobile_label' ); ?>">
						<?php esc_html_e( 'Mobile label', 'jquest-' ); ?>
					</label>
				</th>
				<td>
					<input type="text"
						name="<?php echo esc_attr( $jquest_prefix . 'mobile_label' ); ?>"
						id="<?php echo esc_attr( $jquest_prefix . 'mobile_label' ); ?>"
						value="<?php echo esc_attr( get_option( $jquest_prefix . 'mobile_label', '' ) ); ?>"
						class="regular-text">
				</td>
			</tr>
			<tr class="jquest-section-divider">
				<td colspan="2"><h2><?php esc_html_e( 'Advanced', 'jquest-' ); ?></h2></td>
			</tr>
			<tr>
				<th scope="row">
					<label for="<?php echo esc_attr( $jquest_prefix . 'attach' ); ?>">
						<?php esc_html_e( 'Attach', 'jquest-' ); ?>
					</label>
				</th>
				<td>
					<input type="text"
						name="<?php echo esc_attr( $jquest_prefix . 'attach' ); ?>"
						id="<?php echo esc_attr( $jquest_prefix . 'attach' ); ?>"
						value="<?php echo esc_attr( get_option( $jquest_prefix . 'attach', 'body' ) ); ?>"
						class="regular-text">
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Auto', 'jquest-' ); ?></th>
				<td>
					<input type="hidden" name="<?php echo esc_attr( $jquest_prefix . 'auto' ); ?>" value="0">
					<label>
						<input type="checkbox"
							name="<?php echo esc_attr( $jquest_prefix . 'auto' ); ?>"
							value="1"
							<?php checked( get_option( $jquest_prefix . 'auto', 0 ), 1 ); ?>>
						<?php esc_html_e( 'Automatically open popup', 'jquest-' ); ?>
					</label>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="<?php echo esc_attr( $jquest_prefix . 'limit' ); ?>">
						<?php esc_html_e( 'Limit', 'jquest-' ); ?>
					</label>
				</th>
				<td>
					<input type="number"
						name="<?php echo esc_attr( $jquest_prefix . 'limit' ); ?>"
						id="<?php echo esc_attr( $jquest_prefix . 'limit' ); ?>"
						value="<?php echo esc_attr( get_option( $jquest_prefix . 'limit', 0 ) ); ?>"
						min="0"
						class="small-text">
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Disable dismiss', 'jquest-' ); ?></th>
				<td>
					<input type="hidden" name="<?php echo esc_attr( $jquest_prefix . 'disable_dismiss' ); ?>" value="0">
					<label>
						<input type="checkbox"
							name="<?php echo esc_attr( $jquest_prefix . 'disable_dismiss' ); ?>"
							value="1"
							<?php checked( get_option( $jquest_prefix . 'disable_dismiss', 1 ), 1 ); ?>>
						<?php esc_html_e( 'Disable dismiss on click outside', 'jquest-' ); ?>
					</label>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Disable noscroll', 'jquest-' ); ?></th>
				<td>
					<input type="hidden" name="<?php echo esc_attr( $jquest_prefix . 'disable_noscroll' ); ?>" value="0">
					<label>
						<input type="checkbox"
							name="<?php echo esc_attr( $jquest_prefix . 'disable_noscroll' ); ?>"
							value="1"
							<?php checked( get_option( $jquest_prefix . 'disable_noscroll', 1 ), 1 ); ?>>
						<?php esc_html_e( 'Disable noscroll on body', 'jquest-' ); ?>
					</label>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Use latest script version', 'jquest-' ); ?></th>
				<td>
					<input type="hidden" name="<?php echo esc_attr( $jquest_prefix . 'latest_script' ); ?>" value="0">
					<label>
						<input type="checkbox"
							name="<?php echo esc_attr( $jquest_prefix . 'latest_script' ); ?>"
							value="1"
							<?php checked( get_option( $jquest_prefix . 'latest_script', 0 ), 1 ); ?>>
						<?php esc_html_e( 'Use latest script version', 'jquest-' ); ?>
					</label>
				</td>
			</tr>
		</table>

		<?php submit_button(); ?>
	</form>
	</div>

	<?php endif; ?>
</div>
