<?php
/**
 * Backward-compatible Home composition entrypoint.
 *
 * Phase D1/D2 markup ownership lives in templates/home/content.php. This
 * bridge only prepares presentation context that belongs to the existing Home
 * view. Zero-product orientation remains "Gunakan katalog atau konsultasi
 * teknis" in the composition owner so Home always has an honest next path.
 *
 * @package GrahaSelang
 */

defined( 'ABSPATH' ) || exit;

$home_group_definitions              = self::HOME_GROUPS;
$representative_limit                = 3;
$illustration_urls                   = array(
	'hydraulic_anchor'  => $this->assets->illustration_url( 'hydraulic-hose.svg' ),
	'industrial_anchor' => $this->assets->illustration_url( 'industrial-hose.svg' ),
	'ducting_support'   => $this->assets->illustration_url( 'ducting-hose.svg' ),
	'pvc_support'       => $this->assets->illustration_url( 'pvc-hose.svg' ),
	'fittings_support'  => $this->assets->illustration_url( 'fittings-couplings.svg' ),
	'cng_specialist'    => $this->assets->illustration_url( 'cng-hose.svg' ),
);
$technical_services_illustration_url = $this->assets->illustration_url( 'technical-services.svg' );

include __DIR__ . '/home/content.php';
