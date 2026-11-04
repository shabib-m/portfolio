<?php


// Load the Portfolio setup functions.
require_once backdrop_get_path('theme', 'portfolio') . '/includes/portfolio.setup.inc';

/**
 * Implements hook_form_system_theme_settings_alter().
 */

function portfolio_form_system_theme_settings_alter(&$form, &$form_state, $form_id = NULL) {
  // Avoid conflicts when called by other forms.
  if (isset($form_id)) {
    return;
  }

  // Load Portfolio setup configuration.
  $config = config('portfolio.settings');

  // Main vertical tabs.
  $form['portfolio_tabs'] = array(
    '#type' => 'vertical_tabs',
    '#weight' => -10,
    '#prefix' => '<h2><small>' . t('Advanced Portfolio Theme Settings') . '</small></h2>',
  );

  // ==========================================
  // General appearance settings.
  // ==========================================
  $form['portfolio_social'] = array(
    '#type' => 'fieldset',
    '#title' => t('Footer Social Links'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
    '#group' => 'portfolio_tabs',
  );

  $social_links = array(
    'github' => 'GitHub',
    'linkedin' => 'LinkedIn',
    'youtube' => 'YouTube',
    'instagram' => 'Instagram',
    'facebook' => 'Facebook',
  );

  foreach ($social_links as $key => $label) {
    $form['portfolio_social']['social_' . $key] = array(
      '#type' => 'textfield',
      '#title' => t('@platform URL', array('@platform' => $label)),
      '#default_value' => $config->get('social_' . $key),
      '#description' => t('Leave empty to hide this icon.'),
    );
  }

  // ==========================================
  // Initial site setup.
  // ==========================================
  $form['portfolio_content_settings'] = array(
    '#type' => 'fieldset',
    '#title' => t('Content and Display Settings'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
    '#group' => 'portfolio_tabs',
  );

  // Import taxonomy terms.
  $form['portfolio_content_settings']['import_terms'] = array(
    '#type' => 'checkbox',
    '#title' => t('Import Main Categories'),
    '#default_value' => FALSE,
    '#disabled' => (bool) $config->get('terms_imported'),
    '#description' => t('Import the default Projects and Articles categories for the Portfolio theme.'),
    '#parents' => array('import_terms'),
  );

  // Import default content.
  $form['portfolio_content_settings']['import_content'] = array(
    '#type' => 'checkbox',
    '#title' => t('Import Sample Content'),
    '#default_value' => FALSE,
    '#disabled' => (bool) $config->get('content_imported'),
    '#description' => t('Import sample content using the fields provided by the Portfolio theme.'),
    '#parents' => array('import_content'),
  );

  // Import menu links.
  $form['portfolio_content_settings']['import_menu'] = array(
    '#type' => 'checkbox',
    '#title' => t('Import Menu Links'),
    '#default_value' => FALSE,
    '#disabled' => (bool) $config->get('menu_imported'),
    '#description' => t('Add site page links to the main menu.'),
    '#parents' => array('import_menu'),
  );

  // Import Views blocks.
  $form['portfolio_content_settings']['import_views_block'] = array(
    '#type' => 'checkbox',
    '#title' => t('Import Views Blocks'),
    '#default_value' => FALSE,
    '#disabled' => (bool) $config->get('views_block_imported'),
    '#description' => t('Import Views blocks for displaying site content.'),
    '#parents' => array('import_views_block'),
  );

  // Import Views blocks.
  $form['portfolio_content_settings']['settings_blocks'] = array(
    '#type' => 'checkbox',
    '#title' => t('Configure Layout Blocks'),
    '#default_value' => FALSE,
    '#disabled' => (bool) $config->get('settings_blocks'),
    '#description' => t('Add, remove, and position blocks in the site layouts.'),
    '#parents' => array('settings_blocks'),
  );

  // Custom submit handler.
  $form['#submit'][] = 'portfolio_theme_settings_submit';
}

/**
 * Submit handler for Portfolio theme settings.
 */
function portfolio_theme_settings_submit($form, &$form_state) {

  $config = config('portfolio.settings');

  // Save footer social links.
  $social_links = array(
    'github',
    'linkedin',
    'youtube',
    'instagram',
    'facebook',
  );

  foreach ($social_links as $platform) {
    $key = 'social_' . $platform;

    if (isset($form_state['values'][$key])) {
      $config->set($key, trim($form_state['values'][$key]));
    }
  }

  $config->save();

  // Refresh image style definitions.
  cache()->delete('image_styles');
  backdrop_static_reset('image_styles');
  // Import default taxonomy terms.
  if (!empty($form_state['values']['import_terms']) && !$config->get('terms_imported')) {
    if (portfolio_install_default_terms()) {
      $config->set('terms_imported', TRUE);
      $config->save();

      backdrop_set_message(t('Portfolio default sections have been imported.'));
    }
    else {
      backdrop_set_message(t('Default sections could not be imported. Check the log.'), 'error');
    }
  }

  // Import default menu links.
  if (!empty($form_state['values']['import_menu']) && !$config->get('menu_imported')) {
    if (portfolio_install_default_menu_links()) {
      $config->set('menu_imported', TRUE);
      $config->save();

      backdrop_set_message(t('Portfolio menu links have been imported.'));
    }
    else {
      backdrop_set_message(t('Default menu links could not be imported. Check the log.'), 'error');
    }
  }

  // Import default content.
  if (!empty($form_state['values']['import_content']) && !$config->get('content_imported')) {
    $result = portfolio_install_default_content();

    if ($result !== FALSE) {
      $config->set('content_imported', TRUE);
      $config->save();

      if ($result > 0) {
        backdrop_set_message(t('Imported @count sample content items.', array(
          '@count' => $result,
        )));
      }
      else {
        backdrop_set_message(t('Sample content has already been imported.'));
      }
    }
    else {
      backdrop_set_message(t('Some sample content could not be imported. Check the system log for details.'), 'error');
    }
  }

  // Import Views blocks.
  if (!empty($form_state['values']['import_views_block'])) {
    if (portfolio_install_default_views_blocks()) {
      config('portfolio.settings')
        ->set('views_block_imported', TRUE)
        ->save();

      backdrop_set_message(t('Views blocks have been imported successfully.'));
    }
    else {
      backdrop_set_message(
        t('Views blocks could not be imported. Check the log.'),
        'error'
      );
    }
  }

  // Configure Portfolio layouts.
  if (!empty($form_state['values']['settings_blocks']) && !$config->get('settings_blocks')) {
    if (portfolio_install_default_home_layout()) {
      $config->set('settings_blocks', TRUE);
      $config->save();

      backdrop_set_message(t('Portfolio layouts configured successfully.'));
    }
    else {
      backdrop_set_message(t('Unable to configure Portfolio layouts.'), 'error');
    }
  }
}