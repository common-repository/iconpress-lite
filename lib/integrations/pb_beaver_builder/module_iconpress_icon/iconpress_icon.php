<?php

/**
 * @class IconPress__Bb_IconModule
 */
class IconPress__Bb_IconModule extends FLBuilderModule {

    /**
     * Constructor function for the module. You must pass the
     * name, description, dir and url in an array to the parent class.
     *
     * @method __construct
     */
    public function __construct()
    {
        parent::__construct(array(
            'name'          => __('IconPress Icon', 'iconpress'),
            'description'   => __('An example for coding new modules.', 'iconpress'),
            'category'		=> __('IconPress Modules', 'iconpress'),
            'dir'           =>ICONPRESSLITE_BEAVERBUILDER_DIR . 'module_iconpress_icon/',
            'url'           =>ICONPRESSLITE_BEAVERBUILDER_URL . 'module_iconpress_icon/',
            'editor_export' => true,
            'enabled'       => true,
        ));
        
        /**
         * Use these methods to enqueue css and js already
         * registered or to register and enqueue your own.
         */

        // Register and enqueue your own
        $this->add_css('iconpress-integrations-frontend-css', trailingslashit( ICONPRESSLITE_URI ) . 'lib/integrations/common/styles.css');
        $this->add_js('iconpress-integrations-frontend-js', ICONPRESSLITE_URI . 'lib/integrations/common/scripts.js', ['jquery', 'underscore'], ICONPRESSLITE_VERSION, true);


    }
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('IconPress__Bb_IconModule', array(
    'general'       => array( // Tab
        'title'         => __( 'General', 'iconpress' ), // Tab title
        'sections'      => array( // Tab Sections
            'general'       => array( // Section
                'title'         => '', // Section Title
                'fields'        => array( // Section Fields
                    'icon'          => array(
                        'type'          => 'iconpress_browse_icon',
                        'default'       => 'iconpress-logo',
                        'label'         => __( 'Icontest', 'iconpress' ),
                    ),
                ),
            ),
            'link'          => array(
                'title'         => __( 'Link', 'iconpress' ),
                'fields'        => array(
                    'link'          => array(
                        'type'          => 'link',
                        'label'         => __( 'Link', 'iconpress' ),
                        'preview'       => array(
                            'type'          => 'none',
                        ),
                        'connections'   => array( 'url' ),
                    ),
                    'link_target'   => array(
                        'type'          => 'select',
                        'label'         => __( 'Link Target', 'iconpress' ),
                        'default'       => '_self',
                        'options'       => array(
                            '_self'         => __( 'Same Window', 'iconpress' ),
                            '_blank'        => __( 'New Window', 'iconpress' ),
                        ),
                        'preview'       => array(
                            'type'          => 'none',
                        ),
                    ),
                ),
            ),

        ),
    ),
    'style'         => array( // Tab
        'title'         => __( 'Style', 'iconpress' ), // Tab title
        'sections'      => array( // Tab Sections
            'colors'        => array( // Section
                'title'         => __( 'Colors', 'iconpress' ), // Section Title
                'fields'        => array( // Section Fields
                    'color'         => array(
                        'type'          => 'color',
                        'label'         => __( 'Color', 'iconpress' ),
                        'show_reset'    => true,
                    ),
                    'hover_color' => array(
                        'type'          => 'color',
                        'label'         => __( 'Hover Color', 'iconpress' ),
                        'show_reset'    => true,
                        'preview'       => array(
                            'type'          => 'none',
                        ),
                    ),
                    'bg_color'      => array(
                        'type'          => 'color',
                        'label'         => __( 'Background Color', 'iconpress' ),
                        'show_reset'    => true,
                    ),
                    'bg_hover_color' => array(
                        'type'          => 'color',
                        'label'         => __( 'Background Hover Color', 'iconpress' ),
                        'show_reset'    => true,
                        'preview'       => array(
                            'type'          => 'none',
                        ),
                    ),
                    'three_d'       => array(
                        'type'          => 'select',
                        'label'         => __( 'Gradient', 'iconpress' ),
                        'default'       => '0',
                        'options'       => array(
                            '0'             => __( 'No', 'iconpress' ),
                            '1'             => __( 'Yes', 'iconpress' ),
                        ),
                    ),

                    'icon_rotate' => array(
                        'type'      => 'slider',
                        'label'     => __( 'Icon Rotate' , 'iconpress' ),
                        'settings'  => array (
                            'min'       => '0',                   
                            'max'       => '360',              
                            'value'     => '0',              
                            'range'     => 'min',           
                            'step'      => '1',                   
                            'color'     => '#666666',
                        ),
                    ),

                    'entrance_animation'    => array(
                        'type'              => 'select',
                        'label'             => __( 'Entrance Animation (Special)', 'iconpress' ),
                        'default'           => '',
                        'options'           => array(
                            ''  => __( 'None', 'iconpress' ),
                            'slideReveal' => __( 'Slide Reveal', 'iconpress' ),
                            'explosionReveal' => __( 'Explosion Reveal', 'iconpress' ),
                        ),
                        'toggle'        => array(
                            'slideReveal'        => array(
                                'fields'        => array( 'entrance_delay' ),
                            ),
                            'explosionReveal'        => array(
                                'fields'        => array( 'entrance_delay' ),
                            ),
                        ),
                        'description'   => 'Effect will be displayed in preview mode only. Make sure module\'s default animation is disabled.'
                    ),

                    'entrance_delay' => array(
                        'type'      => 'slider',
                        'label'     => __( 'Entrance delay (ms)' , 'iconpress' ),
                        'settings'  => array (
                            'min'       => '0',                   
                            'max'       => '3000',              
                            'value'     => '0',              
                            'range'     => 'min',           
                            'step'      => '50',                   
                            'color'     => '#666666',
                        ),
                        
                    ),
                
                ),
            ),
            'structure'     => array( // Section
                'title'         => __( 'Structure', 'iconpress' ), // Section Title
                'fields'        => array( // Section Fields
                    // 'size'          => array(
                    //     'type'          => 'text',
                    //     'label'         => __( 'Size', 'iconpress' ),
                    //     'default'       => '30',
                    //     'maxlength'     => '3',
                    //     'size'          => '4',
                    //     'description'   => 'px',
                    //     'sanitize'      => 'absint',
                    // ),
                    'size' => array(
                        'type'      => 'slider',
                        'label'     => __( 'Icon Size' , 'iconpress' ),
                        'settings'  => array (
                            'min'       => '0',                   
                            'max'       => '300',              
                            'value'     => '30',              
                            'range'     => 'min',           
                            'step'      => '1',                   
                            'color'     => '#666666',
                        ),
                    ),
                    'full_size'       => array(
                        'type'          => 'select',
                        'label'         => __( 'Full Size Icon?', 'iconpress' ),
                        'default'       => '0',
                        'options'       => array(
                            '0'             => __( 'No', 'iconpress' ),
                            '1'             => __( 'Yes', 'iconpress' ),
                        ),
                        'toggle'        => array(
                            '0'        => array(
                                'fields'        => array( 'size' ),
                            ),
                        ),
                    ),
                    'align'         => array(
                        'type'          => 'select',
                        'label'         => __( 'Alignment', 'iconpress' ),
                        'default'       => 'left',
                        'options'       => array(
                            'center'        => __( 'Center', 'iconpress' ),
                            'left'          => __( 'Left', 'iconpress' ),
                            'right'         => __( 'Right', 'iconpress' ),
                        ),
                    ),
                ),
            ),
            'r_structure'   => array(
                'title'         => __( 'Mobile Structure', 'iconpress' ),
                'fields'        => array(
                    'r_align'       => array(
                        'type'          => 'select',
                        'label'         => __( 'Alignment', 'iconpress' ),
                        'default'       => 'default',
                        'options'       => array(
                            'default'       => __( 'Default', 'iconpress' ),
                            'custom'        => __( 'Custom', 'iconpress' ),
                        ),
                        'toggle'        => array(
                            'custom'        => array(
                                'fields'        => array( 'r_custom_align' ),
                            ),
                        ),
                    ),
                    'r_custom_align'    => array(
                        'type'              => 'select',
                        'label'             => __( 'Custom Alignment', 'iconpress' ),
                        'default'           => 'left',
                        'options'           => array(
                            'left'              => __( 'Left', 'iconpress' ),
                            'center'            => __( 'Center', 'iconpress' ),
                            'right'             => __( 'Right', 'iconpress' ),
                        ),
                    ),
                ),
            ),

            'decorations'   => array(
                'title'         => __( 'Decorations', 'iconpress' ),
                'fields'        => array(
                    'dc_style'       => array(
                        'type'          => 'select',
                        'label'         => __( 'Select Type', 'iconpress' ),
                        'default'       => '',
                        'options'       => array(
                            ''       => __( 'None', 'iconpress' ),
                            'icon'        => __( 'Icon', 'iconpress' ),
                        ),
                        'toggle'        => array(
                            'icon'        => array(
                                'fields'        => array( 'deko_icon', 'deko_size', 'deko_color', 'deko_posX', 'deko_posY', 'deko_rotate', 'deko_opacity' ),
                                'sections' => array( 'decorations_hover')
                            ),
                        ),
                    ),
                    'deko_icon'          => array(
                        'type'          => 'iconpress_browse_icon',
                        'default'       => '',
                        'label'         => __( 'Select Decoration Icon', 'iconpress' ),
                    ),
                    'deko_size' => array(
                        'type'      => 'slider',
                        'label'     => __( 'Icon Size' , 'iconpress' ),
                        'settings'  => array (
                            'min'       => '20',                   
                            'max'       => '300',              
                            'value'     => '20',              
                            'range'     => 'min',           
                            'step'      => '1',                   
                            'color'     => '#666666',
                        ),
                    ),
                    
                    'deko_color' => array(
                        'type'          => 'color',
                        'label'         => __( 'Color', 'iconpress' ),
                        'default'       => '333333',
                        'show_reset'    => true,
                        'show_alpha'    => true
                    ),
                    'deko_posX'      => array(
                        'type'      => 'slider',
                        'label'     => __( 'Horizontal Position' , 'iconpress' ),
                        'settings'  => array (
                            'min'       => '-3',                   
                            'max'       => '3',              
                            'value'     => '0',              
                            'range'     => 'min',           
                            'step'      => '0.05',                   
                            'color'     => '#666666',
                      ),
                    ),
                    'deko_posY'      => array(
                        'type'      => 'slider',
                        'label'     => __( 'Vertical Position' , 'iconpress' ),
                        'settings'  => array (
                            'min'       => '-3',                   
                            'max'       => '3',              
                            'value'     => '0',              
                            'range'     => 'min',           
                            'step'      => '0.05',                   
                            'color'     => '#666666',
                      ),
                    ),
                    'deko_rotate'      => array(
                        'type'      => 'slider',
                        'label'     => __( 'Rotate' , 'iconpress' ),
                        'settings'  => array (
                            'min'       => '0',                   
                            'max'       => '360',              
                            'value'     => '',              
                            'range'     => 'min',           
                            'step'      => '1',                   
                            'color'     => '#666666',
                        ),
                    ),
                    'deko_opacity'      => array(
                        'type'      => 'slider',
                        'label'     => __( 'Opacity' , 'iconpress' ),
                        'settings'  => array (
                            'min'       => '0.1',                   
                            'max'       => '1',              
                            'value'     => '1',              
                            'range'     => 'max',           
                            'step'      => '0.01',                   
                            'color'     => '#666666',
                      ),
                    ),
              

                    
                ),
            ),

            'decorations_hover'   => array(
                'title'         => __( 'Decorations Hover', 'iconpress' ),
                'fields'        => array(
      
                    'deko_color_hover' => array(
                        'type'          => 'color',
                        'label'         => __( 'Color', 'iconpress' ),
                        'default'       => '333333',
                        'show_reset'    => true,
                        'show_alpha'    => true
                    ),

                    'deko_hover_animation'       => array(
                        'type'          => 'select',
                        'label'         => __( 'Hover Animation', 'iconpress' ),
                        'default'       => '',
                        'options'       => array(
                            ''       => __( 'None', 'iconpress' ),
                            'grow' => 'Grow',
                            'shrink' => 'Shrink',
                            'push' => 'Push',
                            'pop' => 'Pop',
                            'bounce-in' => 'Bounce In',
                            'bounce-out' => 'Bounce Out',
                            'rotate' => 'Rotate',
                            'grow-rotate' => 'Grow Rotate',
                            'float' => 'Float',
                            'sink' => 'Sink',
                            'bob' => 'Bob',
                            'hang' => 'Hang',
                            'buzz' => 'Buzz',
                            'buzz-out' => 'Buzz Out',
                        ),
                        
                    ),

                ),
            ),
        ),
    ),
));
