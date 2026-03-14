<?php
/**
 * JQuest Table class.
 *
 * @package jQuestPlugin
 */

namespace jQuestPlugin;

/**
 * Displays the jQuest games in a WP_List_Table.
 */
class JQuest_table extends \WP_List_Table {

	/**
	 * Returns the columns for the table.
	 *
	 * @return array
	 */
	public function get_columns() {
		return array(
			'Title' => __( 'Title', 'jquest-' ),
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
				return $item->id;
			case 'Title':
				return $item->title;
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
		if ( $which === 'top' && get_option( 'jquest_org_message' ) ) {
			echo esc_html( get_option( 'jquest_org_message' ) );
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
		$this->items           = get_option( 'jquest_org_games', array() );
	}
}
