<?php
/**
 * Templates Customizer Section.
 *
 * @package   Login Designer
 * @author    Rich Tabor from ThatPluginCompany
 * @license   GPL-3.0
 */

// Set template choices.
$template_class = new Login_Designer_Templates();

$wp_customize->add_setting(
	'login_designer[template]', array(
		'default'   => 'default',
		'type'      => 'option',
		'transport' => 'postMessage',
	)
);

$wp_customize->add_control(
	new Login_Designer_Template_Control(
		$wp_customize, 'login_designer[template]', array(
			'type'    => 'login-designer-templates',
			'section' => 'login_designer__section--templates',
			'choices' => $this->get_choices( $template_class->get_templates() ),
		)
	)
);
