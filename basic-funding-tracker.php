<?php
/**
 * Plugin Name: Basic Funding Tracker
 * Plugin URI: http://celloexpressions.com/plugins/basic-funding-tracker/
 * Description: Simple widget that displays the current status of a fundraising goal.
 * Version: 1.0
 * Author: Nick Halsey
 * Author URI: http://celloexpressions.com/
 * Tags: widget, funding, sponsorship, fundraising
 * License: GPL
 
=====================================================================================
Copyright (C) 2014 Nick Halsey

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
=====================================================================================
*/

// Register 'Funding Goal' widget
add_action( 'widgets_init', 'init_basic_funding_goal_widget' );
function init_basic_funding_goal_widget() {
	return register_widget( 'Funding_Goal_Widget' );
}

class Funding_Goal_Widget extends WP_Widget {
	/* constructor */
	function Funding_Goal_Widget() {
		parent::WP_Widget( 'Funding_Goal_Widget', $name = 'Funding Goal' );
	}

	/**
	* This is the Widget output.
	*/
	function widget( $args, $instance ) {
		global $post;
		extract( $args );

		// Widget options
		$title = ( array_key_exists( 'title', $instance ) ) ? apply_filters('widget_title', $instance['title'] ) : '';
		$target = ( array_key_exists( 'target', $instance ) ) ? $instance['target'] : 1000;
		$current = ( array_key_exists( 'current', $instance ) ) ? $instance['current'] : 250;
		$unit = ( array_key_exists( 'unit', $instance ) ) ? $instance['unit'] : '$';

		// Math
		$percentage = intval( $current ) / intval( $target ) * 100;
		$remaining = intval( $target ) - intval( $current );
		$unitcurrent = $unit . $current;
		$unittotal = $unit . $target;
		$unitremaining = $unit . $remaining;

        // Output
		echo $before_widget;
		if ( $title ) {
			echo $before_title . $title . $after_title;
		} ?>

		<div class="funding-goal-container">
			<div class="funding-goal-grid"></div>
			<div class="funding-goal-grid"></div>
			<div class="funding-goal-grid"></div>
			<div class="funding-goal-grid"></div>
			<div class="funding-goal-current" style="height: <?php echo $percentage; ?>%;"></div>
		</div><?php // @todo i18n or make this customizable ?>
		<p>We have raised <strong><?php echo $unitcurrent; ?></strong> out of <strong><?php echo $unittotal; ?></strong>.</p>
		<p><strong><?php echo $unitremaining; ?></strong> left to reach our goal!</p>

		<?php
		echo $after_widget;
	}

	/* Update widget data. */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title']  = $new_instance['title'];
		$instance['target']  = absint( $new_instance['target'] );
		$instance['current'] = absint( $new_instance['current'] );
		$instance['unit']    = $new_instance['unit'];

		return $instance;
	}
	
	/**
	* Widget settings form.
	**/
	function form( $instance ) {
	    if ( $instance ) {
			$title = $instance['title'];
			$target = $instance['target'];
			$current = $instance['current'];
			$unit = $instance['unit'];
	    } else {
			// Defaults.
			$title   = '';
			$target  = 1000;
			$current = 250;
			$unit    = '$';
	    }
		?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php echo __( 'Title:' ); ?></label>
				<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" class="widefat" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'current' ); ?>"><?php echo __( 'Amount Raised:' ); ?></label>
				<input id="<?php echo $this->get_field_id( 'current' ); ?>" name="<?php echo $this->get_field_name('current'); ?>" type="number" value="<?php echo $current; ?>" class="widefat" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'target' ); ?>"><?php echo __( 'Funding Goal:' ); ?></label>
				<input id="<?php echo $this->get_field_id( 'target' ); ?>" name="<?php echo $this->get_field_name('target'); ?>" type="number" value="<?php echo $target; ?>" class="widefat" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'unit' ); ?>"><?php echo __( 'Units:' ); ?></label>
				<input id="<?php echo $this->get_field_id( 'unit' ); ?>" name="<?php echo $this->get_field_name('unit'); ?>" type="text" size="2" value="<?php echo $unit; ?>" />
			</p>
		<?php 
	}
}

add_action( 'wp_head', 'basic_funding_goal_widget_styles' );
function basic_funding_goal_widget_styles() {
	?>
	<style type="text/css">
		.funding-goal-container {
			width: 25%;
			min-width: 80px;
			margin: 0 auto;
			height: 240px;
			border: 2px solid #222;
			background: #fff;
			position: relative;
		}
		.funding-goal-grid {
			width: 100%;
			height: calc(25% - 1px);
			border-top: 1px solid #222;
			background: transparent;
			position: relative;
			z-index: 5;
		}
		.funding-goal-grid:first-child {
			border-top: none;
		}
		.funding-goal-current {
			position: absolute;
			bottom: 0;
			width: 100%;
			height: 50%; /* overriden with current value as % */
			border-top: 1px solid #222;
			background: #900;
			z-index: 2;
		}
	</style>
	<?php
}
?>