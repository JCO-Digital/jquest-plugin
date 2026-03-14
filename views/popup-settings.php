<?php // phpcs:ignore Squiz.Commenting.FileComment.Missing

$jquest_prefix = 'jquest_popup_' . $jquest_lang_key . '_';
$jquest_group  = 'jquest-popup-' . $jquest_lang_key;

?>
<div class="wrap">
	<h1><?php esc_html_e( 'jQuest Popup', 'jquest-' ); ?></h1>

	<?php if ( ! empty( $jquest_tabs ) ) : ?>
		<div class="nav-tab-wrapper">
			<?php foreach ( $jquest_tabs as $jquest_tab => $tab_data ) : ?>
				<a href="<?php echo esc_url( $tab_data['url'] ); ?>"
					class="nav-tab <?php echo $jquest_active_tab === $jquest_tab ? 'nav-tab-active' : ''; ?>"><?php echo esc_html( $tab_data['label'] ); ?></a>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

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
		</table>

		<?php submit_button(); ?>
	</form>
</div>
