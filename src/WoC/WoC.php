<?php # -*- coding: utf-8 -*-

namespace QuickAssortments\WOC\WoC;

/**
 * Class WoC
 *
 * @package QuickAssortments\WOC\WoC
 * @author  Khan Mohammad R. <khan@quickassortments.com>
 * @since   1.0.0
 */
final class WoC {
	/**
	 * @since 1.0.0
	 * @var array
	 */
	private $data;

	/**
	 * Constructor for class WoC
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->data = new Data();
	}

	/**
	 * Returns calculated WoC from the parameters
	 *
	 * @since 1.0.0
	 *
	 * @param int|float $stock
	 * @param int|float $sales
	 * @param int|float $weeks
	 *
	 * @return mixed|float|int
	 */
	public function get_calculated_woc( $stock, $sales, $weeks ) {
		return ( $stock / ( $sales / $weeks ) );
	}

	/**
	 * Method for getting time duration
	 *
	 * @since 1.0.0
	 *
	 * @param int $days
	 * @param int $prod_id
	 *
	 * @return int
	 */
	public function get_time_duration( $days, $prod_id = 0 ) {
		return apply_filters( 'qa_woc_time_duration', $days, $prod_id );
	}

	/**
	 * Method for getting product WoC
	 *
	 * @since 1.0.0
	 *
	 * @param object $product
	 *
	 * @return int
	 */
	public function get_product_woc( $product ) {
		$sales = $this->data->get_simple_products_sales_qty();

		if ( ! isset( $sales[ $product->get_id() ] ) ) {
			return false;
		}

		$stock = $product->get_stock_quantity();
		$sales = $sales[ $product->get_id() ];

		$weeks = 6;
		return $this->get_calculated_woc( $stock, $sales, $weeks );
	}

	/**
	 * Method for getting product variations WoC
	 *
	 * @since 1.0.0
	 *
	 * @param object $child
	 * @param int $stock
	 *
	 * @return bool|int
	 */
	public function get_variation_woc( $child, $stock ) {
		$sales = $this->data->get_variations_sales_qty();

		if ( ! isset( $sales[ $child->get_id() ] ) ) {
			return false;
		}

		$sales = $sales[ $child->get_id() ];

		$weeks = 6;
		return $this->get_calculated_woc( $stock, $sales, $weeks );
	}
}
