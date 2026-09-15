<?php // phpcs:ignore Squiz.Commenting.FileComment.Missing

use SuperQuestPlugin\SuperQuest_Table;

?>
<div class="wrap superquest-wrap">
	<div class="superquest-page-header">
		<img class="superquest-logo" src="<?php echo esc_url( \SuperQuestPlugin\logo_url() ); ?>"
			alt="SuperQuest">
		<h1><?php esc_html_e( 'Settings', 'superquest' ); ?></h1>
	</div>

	<div class="nav-tab-wrapper">
		<?php foreach ( $superquest_tabs as $superquest_tab => $tab_data ) : ?>
			<a href="<?php echo esc_url( $tab_data['url'] ); ?>"
				class="nav-tab <?php echo $superquest_active_tab === $superquest_tab ? 'nav-tab-active' : ''; ?>"><?php echo esc_html( $tab_data['label'] ); ?></a>
		<?php endforeach; ?>
	</div>

	<div class="superquest-card">
		<form method="post" action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>">
			<?php
			if ( $superquest_active_tab === 'general' ) :
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				settings_fields( 'superquest-options-general' );
				do_settings_sections( 'superquest-options-general' );
				submit_button();
			endif;
			?>
		</form>
	</div>

	<?php if ( get_option( 'superquest_org_id' ) ) : ?>
		<form method="post" action="admin-post.php">
			<?php wp_nonce_field( 'my_plugin_button_action_nonce', 'my_plugin_button_action_nonce_field' ); ?>
			<p>
				<button type="submit" name="superquest_refresh_quests" class="button-primary">
					<?php esc_html_e( 'Refresh quests', 'superquest' ); ?>
				</button>
			</p>
			<input type="hidden" name="action" value="superquest_refresh_quests">
		</form>
	<?php endif; ?>

	<div class="superquest-quests-section">
		<h2><?php esc_html_e( 'Organisation Quests', 'superquest' ); ?></h2>
		<?php
		$table = new SuperQuest_Table();
		$table->prepare_items();
		$table->display();
		?>
	</div>
</div>
