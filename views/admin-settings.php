<?php // phpcs:ignore Squiz.Commenting.FileComment.Missing ?>
<div class="wrap">
	<h1><?php esc_html_e( 'jQuest Settings', 'jquest-plugin' ); ?></h1>

	<div class="nav-tab-wrapper">
		<?php foreach ( $jquest_tabs as $jquest_tab => $tab_data ) : ?>
			<a href="<?php echo esc_url( $tab_data['url'] ); ?>"
				class="nav-tab <?php echo $jquest_active_tab === $jquest_tab ? 'nav-tab-active' : ''; ?>"><?php echo esc_html( $tab_data['label'] ); ?></a>
		<?php endforeach; ?>
	</div>

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
