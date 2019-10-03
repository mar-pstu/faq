<?php if ( ! defined( 'ABSPATH' ) ) { exit; }; ?>
<table>
	<tr>
		<td><?php _e( 'Категория', $this->domain ); ?></td>
		<td><?php echo get_term_field( 'name', $fields[ 'term_id' ], $this->category, 'raw' ); ?></td>
	</tr>
	<tr>
		<td><?php _e( 'Сообщение', $this->domain ); ?></td>
		<td><?php echo $fields[ 'message' ]; ?></td>
	</tr>
	<tr>
		<td><?php _e( 'От кого:', $this->domain ); ?></td>
		<td>

			<?php if ( ! empty( $fields[ 'yname' ] ) ) : echo $fields[ 'yname' ]; endif; ?>
			<?php if ( ! empty( trim( $fields[ 'yemail' ] ) ) ) : ?>
				<a href="mailto:<?php echo $fields[ 'yemail' ]; ?>">&lt;<?php echo $fields[ 'yemail' ]; ?>&gt;</a>
			<?php endif; ?>

		</td>
	</tr>
</table>