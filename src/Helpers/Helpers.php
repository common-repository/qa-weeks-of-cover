<?php # -*- coding: utf-8 -*-

namespace QuickAssortments\WOC\Helpers;

/**
 * Class Helpers
 *
 * @package  QuickAssortments\WOC\Helpers
 * @author   Khan Mohammad R. <khan@quickassortments.com>
 * @since    1.0.0
 */
final class Helpers {
	/**
	 * Helper method to get minimum and maximum of an array or numbers
	 *
	 * @since 1.0.0
	 * @param array $args
	 * @return mixed
	 */
	public static function get_min_max( $args = [] ) {
		if ( ! is_array( $args ) || count( $args ) < 2 ) {
			return end( $arg );
		}

		$args = array_filter( $args ); // Removing "zero, empty and null" values from an array

		$data['min'] = array_filter( $args, 'strlen' ) ? min( array_filter( $args, 'strlen' ) ) : 0;
		$data['max'] = max( $args ) ? max( $args ) : 0;

		if ( $data['min'] === $data['max'] ) {
			return $data['min'];
		}

		return $data;
	}

	/**
	 * Helper method to print data in column fields
	 *
	 * @since 1.0.0
	 * @param array $data
	 * @return void
	 */
	public static function formatted_column_data( $data ) {
		if ( empty( $data ) ) {
			echo '–';
			return;
		}

		$data = self::get_min_max( $data );

		if ( is_array( $data ) ) {
			echo $data['min'] . ' – ' . $data['max'];
		} elseif ( ! $data ) {
			echo '–';
		} else {
			echo $data;
		}
	}
}
