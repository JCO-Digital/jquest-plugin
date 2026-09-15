<?php // phpcs:ignore Squiz.Commenting.FileComment.Missing

use jQuestPlugin\Usage_Table;

?>
<div class="wrap jquest-wrap">
	<div class="jquest-page-header">
		<img class="jquest-logo" src="<?php echo esc_url( \jQuestPlugin\logo_url() ); ?>"
			alt="SuperQuest">
		<h1><?php esc_html_e( 'Usage', 'jquest' ); ?></h1>
	</div>

	<div class="jquest-card">
		<p class="description">
			<?php esc_html_e( 'Every page, post and synced pattern whose content holds a SuperQuest block. The list is rebuilt from the database each time this screen is opened, so it is never out of date.', 'jquest' ); ?>
		</p>
		<p class="description">
			<?php
			printf(
				/* translators: %s: link to the popup settings screen. */
				esc_html__( 'Popup quests are not listed here: they are inserted site-wide rather than placed on a page. They are configured on the %s screen.', 'jquest' ),
				'<a href="' . esc_url( admin_url( 'admin.php?page=jquest-popup-v2' ) ) . '">'
					. esc_html__( 'Popup', 'jquest' ) . '</a>'
			);
			?>
		</p>

		<?php
		$jquest_table = new Usage_Table();
		$jquest_table->prepare_items();
		$jquest_table->display();
		?>
	</div>
</div>
