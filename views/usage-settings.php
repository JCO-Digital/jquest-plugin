<?php // phpcs:ignore Squiz.Commenting.FileComment.Missing

use SuperQuestPlugin\Usage_Table;

?>
<div class="wrap superquest-wrap">
	<div class="superquest-page-header">
		<img class="superquest-logo" src="<?php echo esc_url( \SuperQuestPlugin\logo_url() ); ?>"
			alt="SuperQuest">
		<h1><?php esc_html_e( 'Usage', 'superquest' ); ?></h1>
	</div>

	<div class="superquest-card">
		<p class="description">
			<?php esc_html_e( 'Every page, post and synced pattern whose content holds a SuperQuest block. The list is rebuilt from the database each time this screen is opened, so it is never out of date.', 'superquest' ); ?>
		</p>
		<p class="description">
			<?php
			printf(
				/* translators: %s: link to the popup settings screen. */
				esc_html__( 'Popup quests are not listed here: they are inserted site-wide rather than placed on a page. They are configured on the %s screen.', 'superquest' ),
				'<a href="' . esc_url( admin_url( 'admin.php?page=superquest-popup-v2' ) ) . '">'
					. esc_html__( 'Popup', 'superquest' ) . '</a>'
			);
			?>
		</p>

		<?php
		$superquest_table = new Usage_Table();
		$superquest_table->prepare_items();
		$superquest_table->display();
		?>
	</div>
</div>
