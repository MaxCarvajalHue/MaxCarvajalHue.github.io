<?php
/**
 * Brainstorm_Update_Astra_Pro_Sites
 *
 * @package Astra
 * @since 1.0.0
 */

// Ignore the PHPCS warning about constant declaration.
// @codingStandardsIgnoreStart
define( 'BSF_REMOVE_astra-pro-sites_FROM_REGISTRATION_LISTING', true );
// @codingStandardsIgnoreEnd

if ( ! class_exists( 'Brainstorm_Update_Astra_Pro_Sites' ) ) :

	/**
	 * Brainstorm Update
	 */
	class Brainstorm_Update_Astra_Pro_Sites {

		/**
		 * Instance
		 *
		 * @var object Class object.
		 * @access private
		 */
		private static $instance = null;

		/**
		 * Initiator
		 * 
		 * @return mixed Initialized object of class.
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Constructor
		 */
		public function __construct() {

			// Load only the latets Graupi.
			$this->version_check();

			add_action( 'init', array( $this, 'load' ), 999 );
			add_filter( 'bsf_skip_braisntorm_menu', array( $this, 'skip_menu' ) );
			add_filter( 'bsf_skip_author_registration', array( $this, 'skip_menu' ) );
			add_filter( 'bsf_is_product_bundled', array( $this, 'remove_astra_pro_bundled_products' ), 20, 3 );
			add_action( 'bsf_get_plugin_information', array( $this, 'plugin_information' ) ); // @phpstan-ignore-line
			add_filter( 'bsf_license_form_heading_astra-pro-sites', array( $this, 'license_form_titles' ), 10, 3 );
			add_filter( 'bsf_registration_page_url_astra-pro-sites', array( $this, 'license_form_link' ) );
			add_filter( 'bsf_product_activation_notice_astra-pro-sites', array( $this, 'activation_notice' ), 10, 3 );

			add_filter( 'bsf_get_license_message_astra-pro-sites', array( $this, 'license_notice' ), 10 );
			add_action( 'plugin_action_links_' . ASTRA_PRO_SITES_BASE, array( $this, 'license_form_and_links' ), 60 ); // @phpstan-ignore-line

			add_action( 'bsf_activate_license_astra-pro-sites_after_success', array( $this, 'activate_or_deactivate_license' ) );
			add_action( 'bsf_deactivate_license_astra-pro-sites_after_success', array( $this, 'activate_or_deactivate_license' ) );
		}

		/**
		 * License Activate
		 *
		 * @since 2.0.0
		 * @return void
		 */
		public function activate_or_deactivate_license() {
			if ( class_exists( 'Astra_Sites_Batch_Processing_Importer' ) ) {
				// @phpstan-ignore-next-line
				Astra_Sites_Batch_Processing_Importer::get_instance()->set_license_page_builder();
			}
		}

		/**
		 * After License Update
		 * Show action links on the plugin screen.
		 *
		 * Set the default page builder ID to load it by default.
		 *
		 * @since 1.2.4
		 * @param mixed $links Plugin Action links.
		 * @return array<int, string> Filtered plugin action links.
		 */
		public function license_form_and_links( $links = array() ) {

			if ( is_plugin_active( 'astra-sites/astra-sites.php' ) &&
				class_exists( 'BSF_License_Manager' ) &&
				is_callable( array( 'BSF_License_Manager', 'bsf_is_active_license' ) ) &&
				\BSF_License_Manager::bsf_is_active_license( 'astra-pro-sites' )
			) {
				return $links;
			}

			// Enable License form on all single sites in a multisite.
			add_filter( 'bsf_core_popup_license_form_per_network_site_astra-pro-sites', '__return_true' );

			if ( function_exists( 'get_bsf_inline_license_form' ) ) {

				$args = array(
					'product_id'         => 'astra-pro-sites',
					'popup_license_form' => true,
				);

				return get_bsf_inline_license_form( $links, $args, 'edd' );
			}

			add_filter( 'bsf_core_popup_license_form_per_network_site_astra-pro-sites', '__return_false' );

			return $links;
		}


		/**
		 * License Notice
		 *
		 * @since 1.2.4 Updated the license form message if the white label is not set.
		 * @since 1.0.0
		 *
		 * @param  string $purchase_nag Product Purchase nag.
		 * @return string               Purchase nag.
		 */
		public function license_notice( $purchase_nag ) {

			$purchase_url = Astra_Pro_Sites_White_Label::get_option( 'astra-agency', 'licence' );

			// Not have a white label then return the custom nag.
			if ( empty( $purchase_url ) ) {
				/* translators: %1$s product purchase link and %2$s find purchase key link */
				return sprintf( __( '<p>To find your license key, login to  <a target="_blank" href="%1$s">store account</a> and visit the <a target="_blank" href="%2$s">\'Licenses\'</a> page.</p>', 'astra-sites' ), 'https://store.brainstormforce.com/login/', 'https://store.brainstormforce.com/licenses/' ); // phpcs:ignore WordPress.WP.I18n.NoHtmlWrappedStrings
			}

			/* translators: %1$s product purchase link */
			return sprintf( __( '<p>If you don\'t have a license, you can <a target="_blank" href="%1$s">get it here &raquo;</a></p>', 'astra-sites' ), esc_url( $purchase_url ) );
		}

		/**
		 * Product Activation Link
		 *
		 * @since 1.0.0
		 *
		 * @param  string $message      Activation notice message.
		 * @param  string $url          Product activation link.
		 * @param  string $product_name Product Name.
		 * @return mixed               Activation notice.
		 */
		public function activation_notice( $message = '', $url = '', $product_name = '' ) {

			$product_name = Astra_Pro_Sites_White_Label::get_option( 'astra-sites', 'name', ASTRA_PRO_SITES_NAME );

			/* translators: %1$s product activation link %2$s white label plugin name */
			return sprintf( __( 'Please <a href="%1$s">activate</a> your copy of the <i>%2$s</i> to get update notifications, access to support features & other resources!', 'astra-sites' ), $url, $product_name );
		}

		/**
		 * Update brainstorm product version and product path.
		 *
		 * @return void
		 */
		public function version_check() {

			$bsf_core_version_file = realpath( dirname( __FILE__ ) . '/admin/bsf-core/version.yml' );

			// Is file 'version.yml' exist?
			if ( is_string( $bsf_core_version_file ) && is_file( $bsf_core_version_file ) ) {
				global $bsf_core_version, $bsf_core_path;

				if ( null === $bsf_core_version ) {
					$bsf_core_version = '1.0.0';
				}

				$bsf_core_dir = realpath( dirname( __FILE__ ) . '/admin/bsf-core/' );
				// @codingStandardsIgnoreStart
				$version      = (string)file_get_contents( $bsf_core_version_file );
				// @codingStandardsIgnoreEnd

				// Compare versions.
				if ( version_compare( $version, $bsf_core_version, '>' ) ) {
					$bsf_core_version = $version;
					$bsf_core_path    = $bsf_core_dir;
				}
			}
		}

		/**
		 * Remove bundled products for Astra Pro Sites.
		 * For Astra Pro Sites the bundled products are only used for one click plugin installation when importing the Astra Site.
		 * License Validation and product updates are managed separately for all the products.
		 *
		 * @since 1.0.0
		 *
		 * @param  array<string, string> $product_parent  Array of parent product ids.
		 * @param  String                $bsf_product    Product ID or  Product init or Product name based on $search_by.
		 * @param  String                $search_by      Reference to search by id | init | name of the product.
		 *
		 * @return array<string, string>                 Array of parent product ids.
		 */
		public function remove_astra_pro_bundled_products( $product_parent, $bsf_product, $search_by ) {

			// Bundled plugins are installed when the demo is imported on Ajax request and bundled products should be unchanged in the ajax.
			if ( ! defined( 'DOING_AJAX' ) && ! defined( 'WP_CLI' ) ) {

				$key = array_search( 'astra-pro-sites', $product_parent, true );

				if ( false !== $key ) {
					unset( $product_parent[ $key ] );
				}
			}

			return $product_parent;
		}

		/**
		 * Load the brainstorm updater.
		 *
		 * @return void
		 */
		public function load() {
			global $bsf_core_version, $bsf_core_path;
			if ( is_file( (string) realpath( $bsf_core_path . '/index.php' ) ) ) {
				include_once realpath( $bsf_core_path . '/index.php' );
			}
		}

		/**
		 * Install Pluigns Filter
		 *
		 * Add brainstorm bundle products in plugin installer list though filter.
		 *
		 * @since 1.0.0
		 *
		 * @param  array<string, array<string, mixed>> $brainstrom_products   Brainstorm Products.
		 * @return array<string, array<string, mixed>> Brainstorm Products merged with Brainstorm Bundle Products.
		 */
		public function plugin_information( $brainstrom_products = array() ) {

			$main_products = (array) get_option( 'brainstrom_bundled_products', array() );

			foreach ( $main_products as $single_product_key => $single_product ) {
				foreach ( $single_product as $bundle_product_key => $bundle_product ) {

					if ( is_object( $bundle_product ) && isset( $bundle_product->type ) && isset( $bundle_product->slug ) ) {
						$type = $bundle_product->type;
						$slug = $bundle_product->slug;
					} else {
						$type = $bundle_product['type'];
						$slug = $bundle_product['slug'];
					}

					// Add bundled plugin in installer list.
					if ( isset( $slug ) && isset( $type ) && 'plugin' === $type ) {
						$brainstrom_products['plugins'][ $slug ] = (array) $bundle_product;
					}
				}
			}

			return $brainstrom_products;
		}

		/**
		 * License Form Link
		 *
		 * @since 1.0.0
		 *
		 * @param  string $link License form link.
		 * @return string       Popup License form link.
		 */
		public function license_form_link( $link = '' ) {
			return admin_url( 'plugins.php?bsf-inline-license-form=astra-pro-sites' );
		}


		/**
		 * License Form Text.
		 *
		 * @since 1.0.0
		 *
		 * @param  string $form_heading         Form Heading.
		 * @param  string $license_status_class Form status class.
		 * @param  string $license_status       Form status.
		 * @return mixed                        HTML markup of the license form heading.
		 */
		public function license_form_titles( $form_heading = '', $license_status_class = '', $license_status = '' ) {

			if ( 'Active!' === $license_status ) {
				return '<h3>' . __( 'Congratulations!', 'astra-sites' ) . '</h3>';
			}
			if ( 'Not Active!' === $license_status ) {
				/* translators: %1$s white label plugin name */
				return '<h3>' . sprintf( __( 'Activate %1$s License', 'astra-sites' ), Astra_Pro_Sites_White_Label::get_option( 'astra-sites', 'name', ASTRA_PRO_SITES_NAME ) ) . '</h3>';
			}

			return $form_heading;

		}

		/**
		 * Skip Menu.
		 *
		 * @param array<int, string> $products products.
		 * @return array<int, string> $products updated products.
		 */
		public function skip_menu( $products ) {

			$products[] = 'uabb';
			$products[] = 'convertpro';
			$products[] = 'astra-addon';
			$products[] = 'astra-pro-sites';
			$products[] = 'astra-sites-showcase';

			return $products;
		}

	}

	/**
	 * Kicking this off by calling 'get_instance()' method
	 */
	Brainstorm_Update_Astra_Pro_Sites::get_instance();

endif;
