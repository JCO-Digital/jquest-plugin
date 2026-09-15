<?php
/**
 * Block usage table class.
 *
 * @package SuperQuestPlugin
 */

namespace SuperQuestPlugin;

use function SuperQuestPlugin\Usage\scan;

/**
 * Lists every post holding a SuperQuest block in a WP_List_Table.
 */
class Usage_Table extends \WP_List_Table {

	/**
	 * Returns the columns for the table.
	 *
	 * @return array
	 */
	public function get_columns() {
		return array(
			'title'  => __( 'Where', 'superquest' ),
			'id'     => __( 'ID', 'superquest' ),
			'type'   => __( 'Type', 'superquest' ),
			'quests' => __( 'Quests', 'superquest' ),
		);
	}

	/**
	 * Returns the column value.
	 *
	 * @param array  $item        The usage entry.
	 * @param string $column_name The column name.
	 *
	 * @return string
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'title':
				return $this->render_title( $item );
			case 'id':
				return esc_html( (string) $item['id'] );
			case 'type':
				return esc_html( $this->type_label( $item['type'] ) );
			case 'quests':
				return $this->render_quests( $item['quests'] );
			default:
				return '';
		}
	}

	/**
	 * Renders the title cell: the post name, its edit and view links, its status
	 * when it is not simply published, and the pages a synced pattern reaches.
	 *
	 * @param array $item The usage entry.
	 *
	 * @return string
	 */
	private function render_title( array $item ): string {
		$title = '' !== $item['title'] ? $item['title'] : __( '(no title)', 'superquest' );
		$edit  = get_edit_post_link( $item['id'] );

		$out = $edit
			? '<strong><a href="' . esc_url( $edit ) . '">' . esc_html( $title ) . '</a></strong>'
			: '<strong>' . esc_html( $title ) . '</strong>';

		if ( 'publish' !== $item['status'] ) {
			$out .= ' <span class="superquest-usage-status">' . esc_html( $item['status'] ) . '</span>';
		}

		$permalink = 'publish' === $item['status'] ? get_permalink( $item['id'] ) : '';
		if ( $permalink ) {
			$out .= '<div class="row-actions"><span><a href="' . esc_url( $permalink ) . '">'
				. esc_html__( 'View', 'superquest' ) . '</a></span></div>';
		}

		if ( ! empty( $item['hosts'] ) ) {
			$links = array();
			foreach ( $item['hosts'] as $host ) {
				$host_edit  = get_edit_post_link( $host->ID );
				$host_title = '' !== $host->post_title ? $host->post_title : __( '(no title)', 'superquest' );

				$links[] = $host_edit
					? '<a href="' . esc_url( $host_edit ) . '">' . esc_html( $host_title ) . '</a>'
					: esc_html( $host_title );
			}

			$out .= '<div class="superquest-usage-hosts">'
				. esc_html__( 'Used on:', 'superquest' ) . ' ' . implode( ', ', $links )
				. '</div>';
		}

		return $out;
	}

	/**
	 * Renders the quests cell, one line per block on the post.
	 *
	 * @param array $quests The blocks found on the post.
	 *
	 * @return string
	 */
	private function render_quests( array $quests ): string {
		$lines = array();

		foreach ( $quests as $quest ) {
			if ( '' === $quest['quest_id'] ) {
				$lines[] = '<em>' . esc_html__( 'No quest selected', 'superquest' ) . '</em>';
				continue;
			}

			// The title is only known for quests still in the fetched list, so
			// fall back to the stored ID rather than showing an empty cell.
			$line = '' !== $quest['title']
				? esc_html( $quest['title'] )
				: '<code>' . esc_html( $quest['quest_id'] ) . '</code>';

			if ( $quest['popup'] ) {
				$line .= ' <span class="superquest-usage-tag">'
					. ( $quest['auto'] ? esc_html__( 'auto popup', 'superquest' ) : esc_html__( 'popup', 'superquest' ) )
					. '</span>';
			}

			$lines[] = $line;
		}

		return implode( '<br>', $lines );
	}

	/**
	 * A readable name for a post type, falling back to its slug for types that
	 * are not registered on this request.
	 *
	 * @param string $type The post type slug.
	 *
	 * @return string
	 */
	private function type_label( string $type ): string {
		if ( 'wp_block' === $type ) {
			return __( 'Synced pattern', 'superquest' );
		}

		$object = get_post_type_object( $type );

		return $object && isset( $object->labels->singular_name )
			? (string) $object->labels->singular_name
			: $type;
	}

	/**
	 * The message shown when the block is used nowhere.
	 *
	 * @return void
	 */
	public function no_items() {
		esc_html_e( 'No SuperQuest blocks found.', 'superquest' );
	}

	/**
	 * Prepares the items for the table.
	 *
	 * @return void
	 */
	public function prepare_items() {
		$this->_column_headers = array( $this->get_columns(), array(), array() );
		$this->items           = scan();
	}
}
