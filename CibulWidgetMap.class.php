<?php 

  class CibulWidgetMap extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    public function __construct()
    {

      parent::__construct(
        'cibul_widget_map', // Base ID
        'CibulWidgetMap', // Name
        array( 'description' => __( 'A map to show the location of the events listed in your post', 'text_domain' ), ) // Args
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
    public function widget( $args, $instance ) {
      extract( $args );
      $title = apply_filters( 'widget_title', $instance['title'] );

      $this->registerScripts();

      $this->registerStylesheets();

      echo $before_widget;
      if ( ! empty( $title ) )
        echo $before_title . $title . $after_title;

      // here goes the ceueuuude. Should be simple enough

      echo __('<div id="map" class="cibul-widget-map js_cibul_widget_map"></div>', 'text_domain');

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
    public function update( $new_instance, $old_instance ) {
      $instance = array();
      $instance['title'] = strip_tags( $new_instance['title'] );

      return $instance;
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form( $instance ) {
      if ( isset( $instance[ 'title' ] ) ) {
        $title = $instance[ 'title' ];
      }
      else {
        $title = __( '', 'text_domain' );
      }
      ?>
      <p>
      <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
      <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
      </p>
      <?php 
    }

    protected function registerStylesheets()
    {
      wp_register_style( 'map-style', plugins_url('css/cibul-widget-map.css', __FILE__) );
      wp_enqueue_style( 'map-style' );
    }

    protected function registerScripts()
    {
      wp_register_script('googlemaps', 'http://maps.google.com/maps/api/js?sensor=false', false, ''); 
      wp_enqueue_script('googlemaps');

      wp_register_script('cibul-map-widget', plugins_url('js/cibul-widget-map.js', __FILE__));
      wp_enqueue_script('cibul-map-widget');
    }

  }

  // register Foo_Widget widget
  add_action('widgets_init', create_function('', 'register_widget( "CibulWidgetMap" );' ));