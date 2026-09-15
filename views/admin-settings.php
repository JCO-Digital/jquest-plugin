<?php // phpcs:ignore Squiz.Commenting.FileComment.Missing

use jQuestPlugin\JQuest_Table;

?>
<div class="wrap jquest-wrap">
	<div class="jquest-page-header">
		<img class="jquest-logo" src="<?php echo esc_url( \jQuestPlugin\logo_url() ); ?>"
			alt="SuperQuest">
		<h1><?php esc_html_e( 'Settings', 'jquest' ); ?></h1>
	</div>

	<div class="nav-tab-wrapper">
		<?php foreach ( $jquest_tabs as $jquest_tab => $tab_data ) : ?>
			<a href="<?php echo esc_url( $tab_data['url'] ); ?>"
				class="nav-tab <?php echo $jquest_active_tab === $jquest_tab ? 'nav-tab-active' : ''; ?>"><?php echo esc_html( $tab_data['label'] ); ?></a>
		<?php endforeach; ?>
	</div>

	<div class="jquest-card">
		<form method="post" action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>">
			<?php
			if ( $jquest_active_tab === 'general' ) :
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				settings_fields( 'jquest-options-general' );
				do_settings_sections( 'jquest-options-general' );
				submit_button();
			endif;
			?>
		</form>
	</div>

	<?php if ( get_option( 'jquest_org_id' ) ) : ?>
		<form method="post" action="admin-post.php">
			<?php wp_nonce_field( 'my_plugin_button_action_nonce', 'my_plugin_button_action_nonce_field' ); ?>
			<p>
				<button type="submit" name="jquest_refresh_games" class="button-primary">
					<?php esc_html_e( 'Refresh games', 'jquest' ); ?>
				</button>
			</p>
			<input type="hidden" name="action" value="jquest_refresh_games">
		</form>
	<?php endif; ?>

	<div class="jquest-games-section">
		<h2><?php esc_html_e( 'Organisation Games', 'jquest' ); ?></h2>
		<?php
		$table = new JQuest_Table();
		$table->prepare_items();
		$table->display();
		?>
	</div>
</div>
