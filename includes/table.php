<?php
/**
 * Table functions for the plugin.
 *
 * @package jQuestPlugin
 */

namespace jQuestPlugin;

if ( is_admin() ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';

	class jquest_table extends \WP_List_Table
	{
		function get_columns()
		{
			$columns = array(
				'Title'       => __('Title', 'jquest-plugin'),
				'ID'          => "ID",
			);
			return $columns;

		}

		function column_default($item, $column_name)
		{
			switch ($column_name) {
				case 'ID':
					return $item->id;
				case 'Title':
					return $item->title;
				default:
					return "";
			}
		}

		function extra_tablenav($which) {
			if ($which == 'top' && get_option( 'jquest_org_message')) {
				echo get_option( 'jquest_org_message');
			}
		}

		function prepare_items()
		{
			$columns = $this->get_columns();
			$hidden = array();
			$sortable = array();
			$this->_column_headers = array($columns, $hidden, $sortable);

			$this->items = get_option( 'jquest_org_games', [] );
		}

	}

}

