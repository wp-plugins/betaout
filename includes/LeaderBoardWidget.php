<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Adds Foo_Widget widget.
 */
class LeaderBoard extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    public function __construct() {
        parent::__construct(
                'LeaderBoardWidget', // Base ID
                'LeaderBoardWidget', // Name
                array('description' => __('A Social Axis Widget', 'text_domain'),) // Args
        );
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    function widget($args, $instance) {
        extract($args);
        $title = apply_filters('widget_title', empty($instance['title']) ? '&nbsp;' : $instance['title'], $instance, $this->id_base);
        $code = $instance['code'];
        echo $before_widget;
        if ($title)
            echo $before_title . $title . $after_title;
        echo $code;
        echo $after_widget;
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['code'] = $new_instance['code'];
        $instance['title'] = $new_instance['title'];
        return $instance;
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form($instance) {
        if (isset($instance['code'])) {
            $code = $instance['code'];
        } else {
            $code = __('Copy and Paste javascript code...', 'text_domain');
        }
        if (isset($instance['title']))
            $title = $instance['title'];
        else
            $title = '';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
        <p>
            <label for="<?php echo $this->get_field_id('code'); ?>"><?php _e('code:'); ?></label>
            <textarea class="widefat" id="<?php echo $this->get_field_id('code'); ?>" name="<?php echo $this->get_field_name('code'); ?>" onfocus="if(this.value=='Copy and Paste javascript code...')this.value=''" onblur="if(this.value=='')this.value='Copy and Paste javascript code...'"><?php echo $code; ?></textarea>
        </p>
        <?php
    }

}

add_action('widgets_init', create_function('', 'register_widget( "LeaderBoard" );'));
?>
