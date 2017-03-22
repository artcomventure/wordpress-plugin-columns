<div class="wrap">
	<h2><?php printf( __( '%s Settings', 'columns' ), __( 'Editor Columns', 'columns' ) ); ?></h2>

	<form id="columns-options-form" method="post" action="options.php">
		<?php settings_fields( 'columns' );

		// get columns options
		$columns_options = columns_get_options(); ?>

		<table class="form-table">
			<tbody>

			<tr valign="top">
				<th scope="row">
					<?php _e( 'Maximum available columns', 'columns' ); ?>:
				</th>
				<td>
					<input type="text" class="regular-text"
					       value="<?php echo $columns_options['columns']; ?>"
					       placeholder="9"
					       name="columns[columns]"/>

					<p class="description">
						<?php _e( 'Define available number of columns in editor from 2 to 9.', 'columns' ); ?>
					</p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php _e( 'Gap', 'columns' ); ?>:</th>
				<td>
					<input type="text" class="regular-text"
					       value="<?php echo $columns_options['gap']; ?>"
					       placeholder="1.5em"
					       name="columns[gap]"/>

					<p class="description">
						<?php _e( 'Specify the gap between two columns. The value can be set in <code>px</code>, <code>em</code>, <code>rem</code> or <code>%</code>.', 'columns' ); ?>
					</p>

					<?php if ( $columns_options['gap'] && ! preg_match( '/\d+(\.\d+)?(px|em|rem|%)/', $columns_options['gap'] ) ) : ?>
						<div style="display: none;">
							<div class="notice notice-error is-dismissible">
								<p><?php _e( '<b>Please check your gap input.</b> It is wrong and will be ignored. Expected format e.g.: <code>20px</code>, <code>1.5em</code>, ...', 'columns' ); ?></p>
							</div>
						</div>
					<?php endif; ?>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
					<?php _e( 'Add responsive CSS', 'columns' ); ?>:
				</th>
				<td>
					<input type="checkbox" class="regular-checkbox"
					       name="columns[responsive]"
					       value="1"<?php checked( $columns_options['responsive'] ); ?> />
				</td>
			</tr>

			</tbody>
		</table>

		<h3><?php _e( '@media Query Breakpoints', 'columns' ) ?></h3>

		<table class="form-table">
			<tbody>

			<tr valign="top">
				<td colspan="2" style="padding-right: 0; padding-left: 0;">
					<p class="description" style="margin: 0;">
						<?php _e( 'Values in pixels. To exclude a breakpoint just enter a very small value.', 'columns' ); ?>
					</p>
				</td>
			</tr>

			<?php global $content_width;

			foreach ( array( 'Tablet', 'Mobile' ) as $breakpoint ) : ?>

				<tr valign="top">
					<th scope="row"><?php _e( $breakpoint, 'columns' ); ?>:</th>
					<td>
						<input type="text" class="regular-text"
						       value="<?php echo $columns_options[ strtolower( $breakpoint ) ]; ?>"
						       placeholder="<?php echo $breakpoint == 'Tablet' ? floor( $content_width / 3 * 2 ) : floor( $content_width / 2 ); ?>"
						       name="columns[<?php echo strtolower( $breakpoint ); ?>]"/>

						<p class="description">
							<?php if ( $breakpoint == 'Tablet' ) {
								_e( 'Intermediate step. Larger column sets will be displayed in multiple rows.<br />For Example: <code>1x9</code> to <code>3x3</code>, <code>1x8</code> to <code>2x4</code>, <code>1x7</code> to <code>1x4 + 1x3</code>, ...', 'columns' );
							} else {
								_e( 'All columns will be displayed in a single column.', 'columns' );
							} ?>
						</p>
					</td>
				</tr>

			<?php endforeach; ?>

			</tbody>
		</table>

		<?php submit_button(); ?>

	</form>
</div>
