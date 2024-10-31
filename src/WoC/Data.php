<?php # -*- coding: utf-8 -*-

namespace QuickAssortments\WOC\WoC;

if ( ! class_exists( 'WC_Admin_Report' ) ) {
	include_once WC()->plugin_path() . '/includes/admin/reports/class-wc-admin-report.php';
}

/**
 * Class Data
 *
 * @package QuickAssortments\WOC\Data
 * @author  Khan Mohammad R. <khan@quickassortments.com>
 * @since   1.0.0
 */
final class Data extends \WC_Admin_Report {
	/**
	 * @since 1.0.0
	 * @var array
	 */
	private $product_ids = [];

	/**
	 * Report constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->end_date   = time();
		$this->start_date = strtotime( '-42 day', $this->end_date );

		$this->product_ids = get_posts( [
			'fields'         => 'ids', // Only get post IDs
			'posts_per_page' => -1,
			'post_type'      => [ 'product', 'product_variation' ],
		] );
	}

	/**
	 * For getting simple product report data
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_simple_product_report_data() {
		return $this->get_order_report_data(
			[
				'data'         => [
					'_product_id' => [
						'type'            => 'order_item_meta',
						'order_item_type' => 'line_item',
						'function'        => '',
						'name'            => 'product_id',
					],
					'_qty'        => [
						'type'            => 'order_item_meta',
						'order_item_type' => 'line_item',
						'function'        => 'SUM',
						'name'            => 'order_item_count',
					],
				],
				'where_meta'   => [
					'relation' => 'OR',
					[
						'type'       => 'order_item_meta',
						'meta_key'   => [ '_product_id' ], // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
						'meta_value' => array_filter( array_map( 'absint', $this->product_ids ) ), // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
						'operator'   => 'IN',
					],
				],
				'group_by'     => 'product_id',
				'query_type'   => 'get_results',
				'filter_range' => true,
				'order_status' => [ 'completed', 'processing', 'on-hold', 'refunded' ],
			]
		);

	}

	/**
	 * Method for getting simple product sales quantity
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_simple_products_sales_qty() {
		$data = [];
		foreach ( $this->get_simple_product_report_data() as $report ) {
			$data[$report->product_id] = $report->order_item_count;
		}

		return $data;
	}

	/**
	 * Method for getting product variations report data
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_variations_report_data() {
		return $this->get_order_report_data(
			[
				'data'         => [
					'_variation_id' => [
						'type'            => 'order_item_meta',
						'order_item_type' => 'line_item',
						'function'        => '',
						'name'            => 'variation_id',
					],
					'_qty'        => [
						'type'            => 'order_item_meta',
						'order_item_type' => 'line_item',
						'function'        => 'SUM',
						'name'            => 'order_item_count',
					],
				],
				'where_meta'   => [
					'relation' => 'OR',
					[
						'type'       => 'order_item_meta',
						'meta_key'   => [ '_variation_id' ], // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
						'meta_value' => array_filter( array_map( 'absint', $this->product_ids ) ), // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
						'operator'   => 'IN',
					],
				],
				'group_by'     => 'variation_id',
				'query_type'   => 'get_results',
				'filter_range' => true,
				'order_status' => [ 'completed', 'processing', 'on-hold', 'refunded' ],
			]
		);

	}

	/**
	 * Method for getting product variations sales quantity
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_variations_sales_qty() {
		$data = [];
		foreach ( $this->get_variations_report_data() as $report ) {
			$data[ $report->variation_id ] = $report->order_item_count;
		}

		return $data;
	}
}
