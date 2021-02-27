<?php
/**
 * Converter class file.
 *
 * @package cyr-to-lat
 */

namespace Cyr_To_Lat\Settings;

/**
 * Class Converter
 *
 * Settings page "Converter".
 */
class Converter extends PluginSettingsBase {

	/**
	 * Admin script handle.
	 */
	const HANDLE = 'cyr-to-lat-settings';

	/**
	 * Converter nonce.
	 */
	const NONCE = 'cyr-to-lat-converter-nonce';

	/**
	 * Get screen id.
	 *
	 * @return string
	 */
	public function screen_id() {
		return 'settings_page_cyr-to-lat';
	}

	/**
	 * Get option group.
	 *
	 * @return string
	 */
	protected function option_group() {
		return 'cyr_to_lat_group';
	}

	/**
	 * Get option page.
	 *
	 * @return string
	 */
	protected function option_page() {
		return 'cyr-to-lat';
	}

	/**
	 * Get option name.
	 *
	 * @return string
	 */
	protected function option_name() {
		return 'cyr_to_lat_settings';
	}

	/**
	 * Get page title.
	 *
	 * @return string
	 */
	protected function page_title() {
		return __( 'Converter', 'cyr2lat' );
	}

	/**
	 * Get menu title.
	 *
	 * @return string
	 */
	protected function menu_title() {
		return __( 'Cyr To Lat', 'cyr2lat' );
	}

	/**
	 * Get section title.
	 *
	 * @return string
	 */
	protected function section_title() {
		return '';
	}

	/**
	 * Get parent slug.
	 *
	 * @return string
	 */
	protected function parent_slug() {
		return 'options-general.php';
	}

	/**
	 * Init form fields.
	 */
	public function init_form_fields() {
		$this->form_fields = [];
	}

	/**
	 * Init class hooks.
	 */
	protected function init_hooks() {
		parent::init_hooks();

		add_action( 'in_admin_header', [ $this, 'in_admin_header' ] );
	}

	/**
	 * Show settings page.
	 */
	public function settings_page() {
		?>
		<div class="wrap">
			<h2 id="title">
				<?php
				echo( esc_html( __( 'Cyr To Lat Plugin Options', 'cyr2lat' ) ) );
				?>
			</h2>

			<form id="ctl-options" action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>" method="post">
				<?php
				do_settings_sections( $this->option_page() ); // Sections with options.
				settings_fields( $this->option_group() ); // Hidden protection fields.
				?>
			</form>

			<form id="ctl-convert-existing-slugs" action="" method="post">
				<input type="hidden" name="ctl-convert" />
				<?php
				wp_nonce_field( self::NONCE );
				submit_button( __( 'Convert Existing Slugs', 'cyr2lat' ), 'secondary', 'ctl-convert-button' );
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Section callback.
	 *
	 * @param array $arguments Section arguments.
	 */
	public function section_callback( $arguments ) {
	}

	/**
	 * Output convert confirmation popup.
	 */
	public function in_admin_header() {
		if ( ! $this->is_options_screen() ) {
			return;
		}

		?>
		<div id="ctl-confirm-popup">
			<div id="ctl-confirm-content">
				<p>
					<strong><?php esc_html_e( 'Important:', 'cyr2lat' ); ?></strong>
					<?php
					esc_html_e(
						'This operation is irreversible. Please make sure that you have made a backup copy of your database.',
						'cyr2lat'
					);
					?>
				</p>
				<p><?php esc_html_e( 'Are you sure to continue?', 'cyr2lat' ); ?></p>
				<div id="ctl-confirm-buttons">
					<input
						type="button" id="ctl-confirm-ok" class="button button-primary"
						value="<?php esc_html_e( 'OK', 'cyr2lat' ); ?>">
					<button
						type="button" id="ctl-confirm-cancel" class="button button-secondary">
						<?php esc_html_e( 'Cancel', 'cyr2lat' ); ?>
					</button>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Enqueue class scripts.
	 */
	public function admin_enqueue_scripts() {
		if ( ! $this->is_options_screen() ) {
			return;
		}

		wp_enqueue_script(
			self::HANDLE,
			constant( 'CYR_TO_LAT_URL' ) . '/assets/js/converter/app.js',
			[],
			constant( 'CYR_TO_LAT_VERSION' ),
			true
		);

		wp_enqueue_style(
			self::HANDLE,
			constant( 'CYR_TO_LAT_URL' ) . '/assets/css/converter.css',
			[],
			constant( 'CYR_TO_LAT_VERSION' )
		);
	}
}