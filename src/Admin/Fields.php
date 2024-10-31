<?php # -*- coding: utf-8 -*-

namespace QuickAssortments\WOC\Admin;

use QuickAssortments\WOC\WoC\WoC;

/**
 * Class Fields
 *
 * @package  QuickAssortments\WOC\Admin
 * @author   Khan Mohammad R. <khan@quickassortments.com>
 * @since    1.0.0
 */
final class Fields {
	/**
	 * @since 1.0.0
	 * @var string
	 */
	private $prefix = '';

	/**
	 * @since 1.0.0
	 * @var string
	 */
	private $bi = '';

	/**
	 * @since 1.0.0
	 * @var array
	 */
	private $woc = [];

	/**
	 * Fields constructor.
	 * @since 1.0.0
	 * @param string $prefix
	 */
	public function __construct( $prefix = '' ) {
		$this->prefix = $prefix;
		$this->bi     = 'background-image: url("' . QA_COG_BASE_URL . 'assets/img/icon-sq-bg.svg")';
		$this->woc    = new WoC();
	}

	/**
	 * Initiating hooks.
	 * @since 1.0.0
	 */
	public function init() {
		// Adding cost field to product data tab.
		add_action( 'qa_cog_product_data_tab_after', [ $this, 'add_woc_field_to_product_data_tab' ] );
		// Adding fields to variation admin panel
		add_action( 'qa_cog_variation_data_tab_after', [ $this, 'add_woc_field_to_variation_data_tab' ], 10, 2 );
		// Returning the object
		return $this;
	}

	/**
	 * Adding the WoC field to product data tab
	 * @since 1.0.0
	 */
	public function add_woc_field_to_product_data_tab() {
		global $post;

		$product = wc_get_product( $post );
		if ( ! $product instanceof \WC_Product || ! $product->get_manage_stock() ) {
			return;
		}

		$woc = absint( $this->woc->get_product_woc( $product ) );

		$fields['woc'] = [
			'id'                => $this->prefix . 'woc',
			'style'             => $this->bi,
			'class'             => 'qa-input-field',
			'value'             => $woc ? $woc : '–',
			'data_type'         => 'price',
			'placeholder'       => '0',
			'label'             => __( 'Weeks of Cover', 'qa-weeks-of-cover' ),
			'custom_attributes' => [ 'readonly' => 'true' ],
			'desc_tip'          => true,
			'description'       => __( 'Stock management for this product needs to be turned on', 'qa-weeks-of-cover' ),
		];

		$fields = apply_filters( 'qa_woc_product_data_tab_fields', $fields, $product );

		/**
		 * qa_woc_product_data_tab_before action.
		 *
		 * @since 1.0.0
		 * @param \WC_Product $variation
		 * @param int         $loop
		 */
		do_action( 'qa_woc_product_data_tab_before', $product );

		foreach ( $fields as $field ) {
			woocommerce_wp_text_input( $field );
		}

		/**
		 * qa_woc_product_data_tab_after action.
		 *
		 * @since 1.0.0
		 * @param \WC_Product $product
		 * @param int         $loop
		 */
		do_action( 'qa_woc_product_data_tab_after', $product );
	}

	/**
	 * Adding the WoC field to variation data tab
	 *
	 * @since 1.0.0
	 * @param $loop
	 * @param $variation
	 */
	public function add_woc_field_to_variation_data_tab( $variation, $loop ) {
		if ( 'product_variation' !== $variation->post_type ) {
			return;
		}

		$variation = wc_get_product( $variation->get_id() );
		$product   = wc_get_product( $variation->get_parent_id() );

		if ( $variation->get_manage_stock() ) {
			$stock = $variation->get_stock_quantity();
		} elseif ( $product->get_manage_stock() ) {
			$stock = $product->get_stock_quantity();
		} else {
			return;
		}

		$woc = absint( $this->woc->get_variation_woc( $variation, $stock ) );

		$fields['woc'] = [
			'id'                => $this->prefix . "woc_{$loop}",
			'name'              => '',
			'class'             => 'qa-input-field',
			'style'             => $this->bi,
			'value'             => $woc ? $woc : '–',
			'label'             => __( 'Weeks of Cover', 'qa-weeks-of-cover' ),
			'custom_attributes' => [ 'readonly' => 'true' ],
			'wrapper_class'     => 'form-row form-row-first',
			'desc_tip'          => true,
			'description'       => __( 'Stock management for this product needs to be turned on', 'qa-weeks-of-cover' ),
		];

		$fields = apply_filters( 'qa_woc_variation_data_tab_fields', $fields, $variation, $loop );

		/**
		 * qa_woc_variation_data_tab_before action.
		 *
		 * @since 1.0.0
		 * @param \WC_Product_Variation $variation
		 * @param int                   $loop
		 */
		do_action( 'qa_woc_variation_data_tab_before', $variation, $loop );

		foreach ( $fields as $field ) {
			woocommerce_wp_text_input( $field );
		}

		/**
		 * qa_woc_variation_data_tab_after action.
		 *
		 * @since 1.0.0
		 * @param \WC_Product_Variation $variation
		 * @param int                   $loop
		 */
		do_action( 'qa_woc_variation_data_tab_after', $variation, $loop );
	}
}
