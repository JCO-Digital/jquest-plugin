<?php
/**
 * SuperQuest Table class.
 *
 * @package SuperQuestPlugin
 */

namespace SuperQuestPlugin;

/**
 * Displays the SuperQuest quests in a WP_List_Table.
 */
class SuperQuest_Table extends \WP_List_Table {

	/**
	 * Returns the columns for the table.
	 *
	 * @return array
	 */
	public function get_columns() {
		return array(
			'Title' => __( 'Title', 'superquest' ),
			'ID'    => 'ID',
		);
	}

	/**
	 * Returns the default column value.
	 *
	 * @param object $item        The item.
	 * @param string $column_name The column name.
	 *
	 * @return string
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'ID':
				return esc_html( $item->id );
			case 'Title':
				return esc_html( $item->title );
			default:
				return '';
		}
	}

	/**
	 * Renders extra navigation above or below the table.
	 *
	 * @param string $which The position (top or bottom).
	 *
	 * @return void
	 */
	public function extra_tablenav( $which ) {
		if ( $which === 'top' && get_option( 'superquest_org_message' ) ) {
			echo esc_html( get_option( 'superquest_org_message' ) );
		}
	}

	/**
	 * Prepares the items for the table.
	 *
	 * @return void
	 */
	public function prepare_items() {
		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = array();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		$this->items           = get_option( 'superquest_org_quests', array() );
	}
}
