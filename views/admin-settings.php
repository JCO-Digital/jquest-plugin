<?php // phpcs:ignore Squiz.Commenting.FileComment.Missing 

use jQuestPlugin\JQuest_table;

?>
<div class="wrap jquest-wrap">
	<div class="jquest-page-header">
		<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 118 137" fill="#1a2e40" aria-hidden="true"><path d="M19.62,45.96v19.05c0,1.2-1.3,1.95-2.34,1.35L.78,56.84c-.48-.28-.78-.79-.78-1.35v-20.85c0-.56.3-1.07.78-1.35L58.07.21c.48-.28,1.08-.28,1.56,0l16.5,9.53c1.04.6,1.04,2.1,0,2.7L20.4,44.61c-.48.28-.78.79-.78,1.35ZM58.15,114.8L2.41,82.62c-1.04-.6-2.34.15-2.34,1.35v19.05c0,.56.29,1.07.78,1.35l57.29,33.08c.48.28,1.08.28,1.56,0l18.06-10.43c.48-.28.78-.79.78-1.35v-19.05c0-1.2-1.3-1.95-2.34-1.35l-16.5,9.53c-.48.28-1.08.28-1.56,0ZM98.08,45.72v64.35c0,1.2,1.3,1.95,2.34,1.35l16.5-9.52c.48-.28.78-.79.78-1.35V34.39c0-.56-.3-1.07-.78-1.35l-18.06-10.43c-.48-.28-1.08-.28-1.56,0l-16.5,9.53c-1.04.6-1.04,2.1,0,2.7l16.5,9.53c.48.28.78.79.78,1.35ZM77.94,80.54c.38-.3.61-.75.61-1.24v-20.94c0-.49-.23-.94-.61-1.24l-18.31-10.58c-.49-.28-1.08-.28-1.56,0l-18.31,10.58c-.38.3-.61.75-.61,1.24v20.94c0,.49.23.94.61,1.24l18.31,10.58c.47.28,1.07.28,1.56,0l18.31-10.58Z"/></svg>
		<h1><?php esc_html_e( 'jQuest Settings', 'jquest-' ); ?></h1>
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
					<?php esc_html_e( 'Refresh games', 'jquest-' ); ?>
				</button>
			</p>
			<input type="hidden" name="action" value="jquest_refresh_games">
		</form>
	<?php endif; ?>

	<div class="jquest-games-section">
		<h2><?php esc_html_e( 'Organisation Games', 'jquest-' ); ?></h2>
		<?php
		$table = new JQuest_table();
		$table->prepare_items();
		$table->display();
		?>
	</div>
</div>
