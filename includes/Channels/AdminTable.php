<?php
/**
 * Admin list table for channels.
 *
 * @package BlitzDock
 */

namespace BlitzDock\Channels;

use WP_List_Table;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( '\\WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Render the channels list table in the admin interface.
 */
class AdminTable extends WP_List_Table {

	/**
	 * Constructor.
	 *
	 * @since 0.2.0
	 */
	public function __construct() {
		parent::__construct(
			array(
				'singular' => 'blitz_channel',
				'plural'   => 'blitz_channels',
				'screen'   => 'toplevel_page_blitz-dock',
			)
		);
	}

	/**
	 * Prepare the table items.
	 *
	 * @since 0.2.0
	 *
	 * @return void
	 */
	public function prepare_items() : void {
		$per_page     = $this->get_items_per_page( 'blitz_channels_per_page', 20 );
		$current_page = $this->get_pagenum();

		$result = Repository::get_all(
			array(
				'posts_per_page' => $per_page,
				'paged'          => $current_page,
			)
		);

		$this->_column_headers = array( $this->get_columns(), array(), array() );
		$this->items           = $result['items'];

		$this->set_pagination_args(
			array(
				'total_items' => $result['total'],
				'per_page'    => $per_page,
			)
		);
	}

	/**
	 * Define the table columns.
	 *
	 * @since 0.2.0
	 *
	 * @return array<string, string> Column map.
	 */
	public function get_columns() : array {
		return array(
			'channel'      => __( 'Channel', 'blitz-dock' ),
			'channel_link' => __( 'Channel Link', 'blitz-dock' ),
			'today'        => __( 'Today', 'blitz-dock' ),
			'weekly'       => __( 'Weekly', 'blitz-dock' ),
			'monthly'      => __( 'Monthly', 'blitz-dock' ),
			'status'       => __( 'Status', 'blitz-dock' ),
			'actions'      => __( 'Actions', 'blitz-dock' ),
		);
	}

	/**
	 * Default column renderer.
	 *
	 * @since 0.2.0
	 *
	 * @param array  $item        Channel data.
	 * @param string $column_name Column name.
	 * @return string Column output.
	 */
	protected function column_default( $item, $column_name ) : string {
		if ( isset( $item[ $column_name ] ) ) {
			return esc_html( (string) $item[ $column_name ] );
		}

		return '';
	}

	/**
	 * Channel name column.
	 *
	 * @since 0.2.0
	 *
	 * @param array $item Channel data.
	 * @return string Column output.
	 */
	protected function column_channel( $item ) : string {
		return sprintf(
			'<span class="blitz-dock-channel-name">%s</span>',
			esc_html( $item['label'] )
		);
	}

	/**
	 * Channel URL column.
	 *
	 * @since 0.2.0
	 *
	 * @param array $item Channel data.
	 * @return string Column output.
	 */
	protected function column_channel_link( $item ) : string {
		$url = isset( $item['url'] ) ? (string) $item['url'] : '';

		if ( '' === $url ) {
			return $this->render_placeholder_cell();
		}

		$label = $this->format_channel_url( $url );

		return sprintf(
			'<a href="%1$s" target="_blank" rel="noopener noreferrer" class="blitz-dock-channel-link">%2$s</a>',
			esc_url( $url ),
			esc_html( $label )
		);
	}

	/**
	 * Placeholder cell output.
	 *
	 * @since 0.2.0
	 *
	 * @return string Placeholder HTML.
	 */
	private function render_placeholder_cell() : string {
		$label = esc_html__( 'no data', 'blitz-dock' );

                return sprintf(
                        '<span class="blitz-dock-channel-placeholder"><span aria-hidden="true">—</span><span class="screen-reader-text">%s</span></span>',
                        $label
                );
        }

	/**
	 * Today placeholder column.
	 *
	 * @since 0.2.0
	 *
	 * @param array $item Channel data.
	 * @return string Column output.
	 */
	protected function column_today( $item ) : string { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInImplementedInterface
		return $this->render_placeholder_cell();
	}

	/**
	 * Weekly placeholder column.
	 *
	 * @since 0.2.0
	 *
	 * @param array $item Channel data.
	 * @return string Column output.
	 */
	protected function column_weekly( $item ) : string { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInImplementedInterface
		return $this->render_placeholder_cell();
	}

	/**
	 * Monthly placeholder column.
	 *
	 * @since 0.2.0
	 *
	 * @param array $item Channel data.
	 * @return string Column output.
	 */
	protected function column_monthly( $item ) : string { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInImplementedInterface
		return $this->render_placeholder_cell();
	}

	/**
	 * Status column output.
	 *
	 * @since 0.2.0
	 *
	 * @param array $item Channel data.
	 * @return string Column output.
	 */
protected function column_status( $item ) : string {
                $status     = isset( $item['status'] ) ? sanitize_key( $item['status'] ) : 'disabled';
                $channel_id = isset( $item['ID'] ) ? (int) $item['ID'] : 0;

                if ( ! current_user_can( 'manage_options' ) || 0 === $channel_id ) {
                        return $this->render_status_label( $status );
                }

                return $this->render_status_toggle( $channel_id, $status );
        }

	/**
	 * Ensure the table has a consistent class list.
	 *
	 * @since 0.2.0
	 *
	 * @return array<int, string> Class names.
	 */
	protected function get_table_classes() : array {
		$classes = parent::get_table_classes();

		if ( ! in_array( 'blitz-dock-channels__table', $classes, true ) ) {
			$classes[] = 'blitz-dock-channels__table';
		}

		return $classes;
	}

	/**
	 * Actions column output.
	 *
	 * @since 0.2.0
	 *
	 * @param array $item Channel data.
	 * @return string Column output.
	 */
	protected function column_actions( $item ) : string {
		return $this->render_actions( $item );
	}

	/**
	 * Simplified table navigation markup to avoid duplicate headers.
	 *
	 * @since 0.2.0
	 *
	 * @param string $which Navigation placement.
	 * @return void
	 */
	protected function display_tablenav( $which ) : void {
		printf( '<div class="tablenav %s">', esc_attr( $which ) );
		$this->pagination( $which );
		echo '<br class="clear" />';
		echo '</div>';
	}

	/**
	 * Message displayed when no channels exist.
	 *
	 * @since 0.2.0
	 *
	 * @return void
	 */
	public function no_items() : void {
		esc_html_e( 'No channels found.', 'blitz-dock' );
	}

 /**
         * Render a status label.
         *
         * @since 0.2.0
         *
         * @param string $status Status slug.
         * @return string Label markup.
         */
        private function render_status_label( string $status ) : string {
                $status    = sanitize_key( $status );
                $is_active = ( 'active' === $status );
                $label     = $is_active ? __( 'Enabled', 'blitz-dock' ) : __( 'Disabled', 'blitz-dock' );

                return '<span class="blitz-dock-channel-status-text">' . esc_html( $label ) . '</span>';
        }

        /**
         * Render the status toggle form.
         *
         * @since 0.2.0
         *
         * @param int    $channel_id Channel identifier.
         * @param string $status     Current channel status.
         * @return string Toggle markup.
         */
        private function render_status_toggle( int $channel_id, string $status ) : string {
                $is_active    = ( 'active' === sanitize_key( $status ) );
                $state_class  = $is_active ? 'is-on' : 'is-off';
                $aria_checked = $is_active ? 'true' : 'false';
                $action_url   = admin_url( 'admin-post.php' );
                $current_url  = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( (string) $_SERVER['REQUEST_URI'] ) : '';
                $current_url  = is_string( $current_url ) ? esc_url_raw( $current_url ) : '';

                ob_start();
                ?>
                <form method="post" action="<?php echo esc_url( $action_url ); ?>" class="blitz-dock-toggle__form">
                        <input type="hidden" name="action" value="blitz_channel_toggle" />
                        <input type="hidden" name="channel_id" value="<?php echo esc_attr( (string) $channel_id ); ?>" />
                        <input type="hidden" name="redirect_to" value="<?php echo esc_attr( $current_url ); ?>" />
                        <input type="hidden" name="blitz_channel_status" value="disabled" />
                        <?php wp_nonce_field( 'blitz_channel_toggle_' . $channel_id ); ?>
                        <div class="blitz-dock-toggle <?php echo esc_attr( $state_class ); ?>">
                                <input
                                        type="checkbox"
                                        class="blitz-dock-toggle__input"
                                        name="blitz_channel_status"
                                        value="active"
                                        role="switch"
                                        aria-label="<?php esc_attr_e( 'Toggle channel status', 'blitz-dock' ); ?>"
                                        aria-checked="<?php echo esc_attr( $aria_checked ); ?>"
                                        <?php checked( $is_active ); ?>
                                />
                                <span class="blitz-dock-toggle__track" aria-hidden="true">
                                        <span class="blitz-dock-toggle__thumb"></span>
                                </span>
                        </div>
                        <button type="submit" class="blitz-dock-toggle__submit screen-reader-text">
                                <?php esc_html_e( 'Toggle channel status', 'blitz-dock' ); ?>
                        </button>
                </form>
                <?php

                return (string) ob_get_clean();
        }

	/**
	 * Render row action forms.
	 *
	 * @since 0.2.0
	 *
	 * @param array $item Channel data.
	 * @return string Action markup.
	 */
	private function render_actions( array $item ) : string {
		if ( ! current_user_can( 'manage_options' ) ) {
			return '';
		}

	 $channel_id = (int) $item['ID'];
                $action_url = admin_url( 'admin-post.php' );

                ob_start();
                ?>
                <div class="blitz-dock-channel-actions">
                        <form method="post" action="<?php echo esc_url( $action_url ); ?>" class="blitz-dock-channel-actions__form blitz-dock-channel-actions__form--delete">
                                <input type="hidden" name="action" value="blitz_channel_delete" />
                                <input type="hidden" name="channel_id" value="<?php echo esc_attr( (string) $channel_id ); ?>" />
                                <?php wp_nonce_field( 'blitz_channel_delete_' . $channel_id ); ?>
                                <button type="submit" class="button-link delete" data-delete="true">
                                        <?php esc_html_e( 'Delete', 'blitz-dock' ); ?>
                                </button>
                        </form>
                </div>
                <?php

                return (string) ob_get_clean();
        }

	/**
	 * Output table markup with a single header row.
	 *
	 * @since 0.2.0
	 *
	 * @return void
	 */
	public function display() : void {
		$singular = $this->_args['singular'];

		$this->display_tablenav( 'top' );

		echo '<table class="' . esc_attr( implode( ' ', $this->get_table_classes() ) ) . '">';
		echo '<thead><tr>';
		$this->print_column_headers();
		echo '</tr></thead>';

		echo '<tbody id="the-list" data-wp-lists="list:' . esc_attr( $singular ) . '">';
		$this->display_rows_or_placeholder();
		echo '</tbody>';
		echo '</table>';

		$this->display_tablenav( 'bottom' );
	}
	/**
	 * Format a channel URL for admin display.
	 *
	 * @since 0.2.0
	 *
	 * @param string $url Channel URL.
	 * @return string Shortened label for the link.
	 */
	private function format_channel_url( string $url ) : string {
		$display = $url;
		$parsed  = wp_parse_url( $url );

		if ( false !== $parsed && is_array( $parsed ) ) {
			$display = $parsed['host'] ?? '';

			if ( isset( $parsed['path'] ) ) {
				$display .= rtrim( $parsed['path'], '/' );
			}

			if ( isset( $parsed['query'] ) && '' !== $parsed['query'] ) {
				$display .= '?' . $parsed['query'];
			}
		}

		$display = preg_replace( '#^https?://#i', '', (string) $display );
		$display = is_string( $display ) ? trim( $display ) : '';

		if ( '' === $display ) {
			$display = $url;
		}

		return wp_html_excerpt( $display, 60, '&hellip;' );
	}
}