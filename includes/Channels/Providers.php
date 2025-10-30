<?php
/**
 * Channel provider registry.
 *
 * @package BlitzDock
 */

namespace BlitzDock\Channels;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Supported channel providers.
 */
class Providers {

	/**
	 * Retrieve all providers.
	 *
	 * @since 0.2.0
	 *
	 * @return array<string, array{label:string,regex:string,icon:string}> Providers map.
	 */
	public static function all() : array {
		return array(
			'whatsapp' => array(
				'label' => __( 'WhatsApp', 'blitz-dock' ),
				'regex' => '~^https://wa\\.me/\\d{7,15}$~',
				'icon'  => 'whatsapp',
			),
		);
	}

	/**
	 * Determine whether a provider is registered.
	 *
	 * @since 0.2.0
	 *
	 * @param string $slug Provider slug.
	 * @return bool Whether the provider exists.
	 */
	public static function exists( string $slug ) : bool {
		$slug = sanitize_key( $slug );

		return isset( self::all()[ $slug ] );
	}

	/**
	 * Retrieve the label for a provider.
	 *
	 * @since 0.2.0
	 *
	 * @param string $slug Provider slug.
	 * @return string Provider label.
	 */
	public static function label( string $slug ) : string {
		$slug = sanitize_key( $slug );
		$all  = self::all();

		if ( isset( $all[ $slug ]['label'] ) ) {
			return $all[ $slug ]['label'];
		}

		return ucfirst( $slug );
	}

	/**
	 * Retrieve the validator regex for a provider.
	 *
	 * @since 0.2.0
	 *
	 * @param string $slug Provider slug.
	 * @return string Regex pattern.
	 */
	public static function validator( string $slug ) : string {
		$slug = sanitize_key( $slug );
		$all  = self::all();

		if ( isset( $all[ $slug ]['regex'] ) ) {
			return $all[ $slug ]['regex'];
		}

		return '';
	}
}