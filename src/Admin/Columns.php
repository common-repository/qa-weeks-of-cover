<?php # -*- coding: utf-8 -*-

namespace QuickAssortments\WOC\Admin;

use QuickAssortments\WOC\Helpers\Helpers;
use QuickAssortments\WOC\WoC\WoC;

/**
 * Class Columns
 *
 * @package QuickAssortments\WOC\Columns
 * @author  Khan Mohammad R. <khan@quickassortments.com>
 * @since   1.0.0
 */
final class Columns {
	/**
	 * @since 1.0.0
	 * @var array
	 */
	private $woc;

	/**
	 * Columns constructor.
	 * @since 1.0.0
	 */
	public function __construct() {
		// Constructor code here
		$this->woc = new WoC();
	}

	/**
	 * Initiating hooks.
	 *
	 * @since 1.0.0
	 *
	 * @return object
	 */
	public function init() {
		// Adding columns to product backend.
		add_filter( 'qa_cog_additional_columns', [ $this, 'additional_columns' ], 10, 1 );

		// Adding value to the custom columns at products backend.
		add_action( 'manage_product_posts_custom_column', [ $this, 'column_cost_price_and_stock_value_data' ], 10, 2 );

		return $this;
	}

	/**
	 * Method for adding additional columns to the product table
	 *
	 * @since 1.0.0
	 *
	 * @param array $columns
	 *
	 * @return array
	 */
	public function additional_columns( $columns ) {
		$return = [];
		foreach ( $columns as $k => $n ) {
			$return[ $k ] = $n;

			if ( 'price' !== $k ) {
				continue;
			}

			$return['woc'] = esc_html__( 'WoC', 'qa-cost-of-goods-margins' );
		}
		return apply_filters( 'qa_woc_additional_columns', $return );
	}

	/**
	 * Columns cost data and stock value calculation
	 *
	 * @since 1.0.0
	 *
	 * @param  string $column
	 * @param  int $post_id
	 *
	 * @return void
	 */
	public function column_cost_price_and_stock_value_data( $column, $post_id ) {
		// Instantiating individual product.
		$product = wc_get_product( $post_id );

		if ( $product->is_type( 'variable' ) ) {
			$this->variable_products_column_data( $column, $product );
		} elseif ( $product->is_type( 'grouped' ) ) {
			$this->grouped_products_column_data( $column, $product );
		} else {
			$this->general_products_column_data( $column, $product );
		}
	}

	/**
	 * General products columns data
	 *
	 * @since 1.0.0
	 *
	 * @param string $column
	 * @param object $product
	 *
	 * @return void
	 */
	public function general_products_column_data( $column, $product ) {
		$woc = '–';
		if ( $product->get_manage_stock() ) {
			$woc = absint( $this->woc->get_product_woc( $product ) );
		}
		switch ( $column ) {
			case 'woc':
				echo $woc;
				break;
		}
	}

	/**
	 * Grouped products column data
	 *
	 * @since 1.0.0
	 *
	 * @param string $column
	 * @param object $product
	 *
	 * @return void
	 */
	public function grouped_products_column_data( $column, $product ) {
		$children = $product->get_children();
		$data = [];
		foreach ( $children as $child ) {
			$child = wc_get_product( $child );
			if ( $child->get_manage_stock() ) {
				$data[ $child->get_id() ] = absint( $this->woc->get_product_woc( $child ) );
			}
		}

		switch ( $column ) {
			case 'woc':
				Helpers::formatted_column_data( $data );
				break;
		}
	}

	/**
	 * Variable products column data
	 *
	 * @since 1.0.0
	 *
	 * @param string $column
	 * @param object $product
	 *
	 * @return void
	 */
	public function variable_products_column_data( $column, $product ) {
		$children = $product->get_children();
		$data = [];
		foreach ( $children as $child ) {
			$child = wc_get_product( $child );
			if ( $child->get_manage_stock() ) {
				$data[ $child->get_id() ] = absint( $this->woc->get_variation_woc( $child, $child->get_stock_quantity() ) );
			} else if ( $product->get_manage_stock() ) {
				$data[ $child->get_id() ] = absint( $this->woc->get_variation_woc( $child, $product->get_stock_quantity() ) );
			}
		}

		switch ( $column ) {
			case 'woc':
				Helpers::formatted_column_data( $data );
				break;
		}
	}
}
