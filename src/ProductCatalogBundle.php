<?php

namespace GrahaSelang;

defined( 'ABSPATH' ) || exit;

/** Fixed-path validator/cleanup helper for the one approved product bundle. */
final class ProductCatalogBundle {
	const RUNTIME_RELATIVE_PATH = 'migration-runtime/product-catalog-v1';
	const MANIFEST_FILE         = 'manifest.json';
	const MAX_MANIFEST_BYTES    = 65536;
	const MAX_DATA_BYTES        = 5242880;
	const HOME_GROUPS           = array( 'hydraulic_anchor', 'industrial_anchor', 'ducting_support', 'pvc_support', 'fittings_support', 'cng_specialist' );

	/** @var string */
	private $runtime_dir;

	public function __construct( $plugin_file ) {
		$this->runtime_dir = trailingslashit( plugin_dir_path( $plugin_file ) ) . self::RUNTIME_RELATIVE_PATH;
	}

	public function runtime_dir() {
		return $this->runtime_dir;
	}

	/**
	 * Cheap structural validation used by admin menu/status rendering.
	 * It deliberately does not hash payloads or import/query products.
	 *
	 * @return array<string,mixed>|\WP_Error
	 */
	public function read_header() {
		$path = $this->runtime_dir . '/' . self::MANIFEST_FILE;
		if ( ! is_file( $path ) ) {
			return array();
		}
		$size = filesize( $path );
		if ( false === $size || $size > self::MAX_MANIFEST_BYTES ) {
			return new \WP_Error( 'graha_manifest_size', 'Manifest migrasi melebihi batas aman.' );
		}
		$raw = file_get_contents( $path );
		if ( false === $raw ) {
			return new \WP_Error( 'graha_manifest_read', 'Manifest migrasi tidak dapat dibaca.' );
		}
		$data = json_decode( $raw, true );
		if ( ! is_array( $data ) ) {
			return new \WP_Error( 'graha_manifest_json', 'Manifest migrasi bukan JSON yang valid.' );
		}
		return $this->validate_manifest_structure( $data );
	}

	/** Full AJAX-time validation. */
	public function validate() {
		$manifest = $this->decode_json_file( $this->runtime_dir . '/' . self::MANIFEST_FILE, self::MAX_MANIFEST_BYTES, 'manifest' );
		$header   = $this->validate_manifest_structure( $manifest );
		if ( is_wp_error( $header ) ) {
			throw new \RuntimeException( $header->get_error_message() );
		}

		$product_path = $this->confined_file( 'products.json' );
		$actual_sha   = hash_file( 'sha256', $product_path );
		$expected_sha = strtolower( trim( (string) $manifest['checksums']['products.json'] ) );
		if ( ! is_string( $actual_sha ) || ! hash_equals( $expected_sha, strtolower( $actual_sha ) ) ) {
			throw new \RuntimeException( 'Checksum products.json tidak cocok.' );
		}

		$data = $this->decode_json_file( $product_path, self::MAX_DATA_BYTES, 'products' );
		if ( ! isset( $data['products'] ) || ! is_array( $data['products'] ) ) {
			throw new \RuntimeException( 'Payload products.json tidak memiliki array products.' );
		}

		$products = array();
		$seen     = array();
		foreach ( $data['products'] as $record ) {
			if ( ! is_array( $record ) ) {
				throw new \RuntimeException( 'Salah satu record produk bukan object yang valid.' );
			}
			$product = $this->normalize_record( $record );
			if ( isset( $seen[ $product['source_id'] ] ) ) {
				throw new \RuntimeException( 'Source identity duplikat di dalam bundle: ' . $product['source_id'] );
			}
			$seen[ $product['source_id'] ] = true;
			$products[] = $product;
		}
		if ( (int) $manifest['expected_records'] !== count( $products ) ) {
			throw new \RuntimeException( 'Jumlah record tidak sama dengan expected_records pada manifest.' );
		}
		return array( 'manifest' => $manifest, 'products' => $products );
	}

	/** Remove only declared payload + manifest in the fixed runtime directory. */
	public function cleanup( array $manifest ) {
		$errors = array();
		foreach ( $manifest['files'] as $relative ) {
			try {
				$path = $this->confined_file( (string) $relative );
				if ( is_file( $path ) && ! @unlink( $path ) ) {
					$errors[] = 'Gagal menghapus ' . basename( $path ) . '.';
				}
			} catch ( \Throwable $error ) {
				$errors[] = $error->getMessage();
			}
		}
		$manifest_path = $this->runtime_dir . '/' . self::MANIFEST_FILE;
		if ( is_file( $manifest_path ) && ! @unlink( $manifest_path ) ) {
			$errors[] = 'Gagal menghapus manifest runtime.';
		}
		if ( is_dir( $this->runtime_dir ) && ! @rmdir( $this->runtime_dir ) ) {
			$errors[] = 'Folder runtime bundle tidak dapat dihapus.';
		}
		return array( 'ok' => empty( $errors ), 'message' => implode( ' ', $errors ) );
	}

	/** @return array<string,mixed>|\WP_Error */
	private function validate_manifest_structure( array $data ) {
		foreach ( array( 'bundle_id', 'schema_version', 'source_version', 'migration_type', 'files', 'checksums', 'expected_records' ) as $field ) {
			if ( ! array_key_exists( $field, $data ) || ( ! is_array( $data[ $field ] ) && '' === (string) $data[ $field ] ) ) {
				return new \WP_Error( 'graha_manifest_field', 'Manifest migrasi belum lengkap.' );
			}
		}
		if ( 'product-catalog' !== $data['migration_type'] || '1' !== (string) $data['schema_version'] ) {
			return new \WP_Error( 'graha_manifest_type', 'Tipe/schema bundle migrasi tidak didukung.' );
		}
		if ( ! is_array( $data['files'] ) || array( 'products.json' ) !== array_values( $data['files'] ) ) {
			return new \WP_Error( 'graha_manifest_files', 'Daftar file manifest migrasi tidak valid.' );
		}
		$checksum = is_array( $data['checksums'] ) && isset( $data['checksums']['products.json'] ) ? strtolower( trim( (string) $data['checksums']['products.json'] ) ) : '';
		if ( ! preg_match( '/^[a-f0-9]{64}$/', $checksum ) ) {
			return new \WP_Error( 'graha_manifest_checksum', 'Checksum products.json pada manifest tidak valid.' );
		}
		if ( (int) $data['expected_records'] < 1 ) {
			return new \WP_Error( 'graha_manifest_count', 'expected_records pada manifest tidak valid.' );
		}
		return $data;
	}

	private function normalize_record( array $record ) {
		$product = array(
			'source_id'  => isset( $record['source_id'] ) ? trim( (string) $record['source_id'] ) : '',
			'name'       => isset( $record['name'] ) ? trim( wp_strip_all_tags( (string) $record['name'] ) ) : '',
			'slug'       => isset( $record['slug'] ) ? sanitize_title( (string) $record['slug'] ) : '',
			'source_url' => isset( $record['source_url'] ) ? esc_url_raw( (string) $record['source_url'] ) : '',
			'home_group' => isset( $record['home_group'] ) ? sanitize_key( (string) $record['home_group'] ) : '',
		);
		if ( '' === $product['source_id'] || '' === $product['name'] || '' === $product['slug'] ) {
			throw new \RuntimeException( 'Setiap produk wajib memiliki source_id, name, dan slug.' );
		}
		if ( ! preg_match( '/^graha-public-product:[a-z0-9-]+$/', $product['source_id'] ) ) {
			throw new \RuntimeException( 'Format source_id tidak valid: ' . $product['source_id'] );
		}
		if ( ! in_array( $product['home_group'], self::HOME_GROUPS, true ) ) {
			throw new \RuntimeException( 'home_group tidak valid untuk ' . $product['source_id'] );
		}
		return $product;
	}

	private function decode_json_file( $path, $max_bytes, $label ) {
		if ( ! is_file( $path ) ) {
			throw new \RuntimeException( sprintf( 'File %s tidak ditemukan.', $label ) );
		}
		$size = filesize( $path );
		if ( false === $size || $size > $max_bytes ) {
			throw new \RuntimeException( sprintf( 'Ukuran file %s tidak valid.', $label ) );
		}
		$raw = file_get_contents( $path );
		if ( false === $raw ) {
			throw new \RuntimeException( sprintf( 'File %s tidak dapat dibaca.', $label ) );
		}
		$data = json_decode( $raw, true );
		if ( ! is_array( $data ) ) {
			throw new \RuntimeException( sprintf( 'File %s bukan JSON yang valid.', $label ) );
		}
		return $data;
	}

	private function confined_file( $relative ) {
		$relative = (string) $relative;
		if ( '' === $relative || false !== strpos( $relative, '..' ) || '/' === substr( $relative, 0, 1 ) || false !== strpos( $relative, '\\' ) || false !== strpos( $relative, ':' ) ) {
			throw new \RuntimeException( 'Path bundle tidak aman.' );
		}
		$root = realpath( $this->runtime_dir );
		$path = realpath( $this->runtime_dir . '/' . $relative );
		if ( false === $root || false === $path || 0 !== strpos( $path, $root . DIRECTORY_SEPARATOR ) ) {
			throw new \RuntimeException( 'File bundle berada di luar runtime bundle yang diizinkan.' );
		}
		return $path;
	}
}
