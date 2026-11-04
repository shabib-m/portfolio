<?php
/**
 * Import default taxonomy terms for Portfolio theme.
 */
function portfolio_install_default_terms() {

  $file = backdrop_get_path('theme', 'portfolio') . '/settings-content/taxonomy/taxonomy.terms.portfolio_main.json';

  // Check that the vocabulary exists.
  if (!taxonomy_vocabulary_load('portfolio_main')) {
    return FALSE;
  }

  // Check that the JSON file exists.
  if (!file_exists($file)) {
    return FALSE;
  }

  // Read and decode JSON.
  $terms = backdrop_json_decode(file_get_contents($file));

  if (!is_array($terms) || empty($terms)) {
    return FALSE;
  }

  $success = TRUE;

  foreach ($terms as $item) {

    if (empty($item['name'])) {
      $success = FALSE;
      continue;
    }

    // Skip existing terms.
    $exists = db_select('taxonomy_term_data', 't')
      ->condition('vocabulary', 'portfolio_main')
      ->condition('name', $item['name'])
      ->countQuery()
      ->execute()
      ->fetchField();

    if ($exists) {
      continue;
    }

    // Create taxonomy term.
    $term = entity_create('taxonomy_term', array(
      'name' => $item['name'],
      'vocabulary' => 'portfolio_main',
      'description' => isset($item['description']) ? $item['description'] : '',
      'weight' => isset($item['weight']) ? $item['weight'] : 0,
      'parent' => array(0),
    ));

    taxonomy_term_save($term);

    // Check that the term was saved.
    if (empty($term->tid)) {
      $success = FALSE;
      continue;
    }

    // Add URL alias.
    if (!empty($item['path']) && module_exists('path')) {

      $alias_exists = db_select('url_alias', 'a')
        ->condition('alias', $item['path'])
        ->countQuery()
        ->execute()
        ->fetchField();

      if (!$alias_exists) {
        $path = array(
          'source' => 'taxonomy/term/' . $term->tid,
          'alias' => $item['path'],
          'langcode' => LANGUAGE_NONE,
        );

        path_save($path);
      }
    }
  }

  return $success;
}

/**
 * Import default menu links for Portfolio theme.
 */
function portfolio_install_default_menu_links() {

  $file = backdrop_get_path('theme', 'portfolio') . '/settings-content/menu/portfolio.menu-links.json';

  // Check that the menu exists.
  if (!isset(menu_list_system_menus()['main-menu'])) {
    return FALSE;
  }

  // Check that the JSON file exists.
  if (!file_exists($file)) {
    return FALSE;
  }

  // Read and decode JSON.
  $links = backdrop_json_decode(file_get_contents($file));

  if (!is_array($links) || empty($links)) {
    return FALSE;
  }

  $success = TRUE;

  foreach ($links as $item) {

    if (empty($item['title']) || empty($item['path'])) {
      $success = FALSE;
      continue;
    }

    // Get the internal system path.
    $normal_path = backdrop_get_normal_path($item['path']);

    // Skip existing menu links.
    $existing = db_select('menu_links', 'm')
      ->fields('m', array('mlid'))
      ->condition('menu_name', 'main-menu')
      ->condition('link_path', $normal_path)
      ->execute()
      ->fetchField();

    if ($existing) {
      continue;
    }

    // Create a new menu link.
    $link = array(
      'link_title' => $item['title'],
      'link_path' => $normal_path,
      'menu_name' => 'main-menu',
      'weight' => isset($item['weight']) ? $item['weight'] : 0,
      'langcode' => LANGUAGE_NONE,
      'module' => 'menu',
      'customized' => 1,
      'hidden' => 0,
    );

    $result = menu_link_save($link);

    if (!$result) {
      $success = FALSE;
    }
  }

  return $success;
}

/**
 * Import default content for the Portfolio theme.
 */
function portfolio_install_default_content() {
  $file = backdrop_get_path('theme', 'portfolio') . '/settings-content/content/portfolio.default-content.json';

  if (!file_exists($file) || !module_exists('path')) {
    return FALSE;
  }

  // Read and decode JSON.
  $data = backdrop_json_decode(file_get_contents($file));

  if (empty($data['content']) || !is_array($data['content']) || empty($data['vocabulary'])) {
    return FALSE;
  }

  // Check that the taxonomy vocabulary exists.
  if (!taxonomy_vocabulary_load($data['vocabulary'])) {
    return FALSE;
  }

  // Get available content types.
  $types = node_type_get_types();

  // Load imported content records.
  $config = config('portfolio.settings');
  $imported = $config->get('content_imported_paths');

  if (!is_array($imported)) {
    $imported = array();
  }

  global $user;

  $created = 0;
  $success = TRUE;

  foreach ($data['content'] as $item) {

    if (empty($item['type']) || empty($item['title']) || empty($item['path'])) {
      $success = FALSE;
      continue;
    }

    $type = $item['type'];
    $path = $item['path'];

    // Skip previously imported content, even if deleted later.
    if (isset($imported[$path])) {
      continue;
    }

    // Check that the content type exists.
    if (!isset($types[$type])) {
      watchdog('portfolio', 'Content type not found: @type', array('@type' => $type), WATCHDOG_WARNING);
      $success = FALSE;
      continue;
    }

    // Check required fields.
    $required_fields = array(
      'body',
      'field_image',
    );

    if ($type == 'post') {
      $required_fields = array_merge($required_fields, array(
        'field_portfolio_main',
        'field_block_home',
        'field_block_project',
        'field_block_article',
      ));
    }

    foreach ($required_fields as $field_name) {
      if (!field_info_instance('node', $field_name, $type)) {
        watchdog('portfolio', 'Field @field not found on content type @type.', array(
          '@field' => $field_name,
          '@type' => $type,
        ), WATCHDOG_WARNING);

        $success = FALSE;
        continue 2;
      }
    }

    // Do not overwrite an existing URL alias.
    $alias_exists = db_select('url_alias', 'a')
      ->condition('alias', $path)
      ->countQuery()
      ->execute()
      ->fetchField();

    if ($alias_exists) {
      watchdog('portfolio', 'URL alias already exists: @path', array('@path' => $path), WATCHDOG_WARNING);
      $success = FALSE;
      continue;
    }

    // Create the node.
    $node = entity_create('node', array(
      'type' => $type,
      'title' => $item['title'],
      'uid' => isset($user->uid) ? $user->uid : 1,
      'status' => isset($item['status']) ? (int) $item['status'] : 1,
      'promote' => 0,
      'langcode' => LANGUAGE_NONE,
      'created' => REQUEST_TIME,
      'changed' => REQUEST_TIME,
    ));

    // Add the body.
    if (isset($item['body']['value'])) {
      $node->body[LANGUAGE_NONE][0] = array(
        'value' => $item['body']['value'],
        'format' => 'filtered_html',
      );
    }

    // Add the image.
    if (!empty($item['field_image']['file'])) {

      $image_filename = basename($item['field_image']['file']);

      // Make sure the filename does not contain a directory path.
      if ($image_filename != $item['field_image']['file']) {
        watchdog('portfolio', 'Invalid image filename: @file', array(
          '@file' => $item['field_image']['file'],
        ), WATCHDOG_WARNING);

        $success = FALSE;
        continue;
      }

      // Only import supported raster image formats.
      $extension = strtolower(pathinfo($image_filename, PATHINFO_EXTENSION));
      if (!in_array($extension, array('png', 'jpg', 'jpeg', 'webp', 'gif'), TRUE)) {
        watchdog('portfolio', 'Unsupported default image extension: @file', array(
          '@file' => $image_filename,
        ), WATCHDOG_WARNING);
        $success = FALSE;
        continue;
      }

      // Get the image from the theme directory.
      $image_source = backdrop_get_path('theme', 'portfolio') . '/settings-content/content/images/' . $image_filename;

      if (!is_file($image_source)) {
        watchdog('portfolio', 'Default image not found: @file', array(
          '@file' => $image_filename,
        ), WATCHDOG_WARNING);

        $success = FALSE;
        continue;
      }

      // Verify that the source is an actual image of the expected type.
      $image_info = @getimagesize($image_source);
      $allowed_types = array(
        'jpg' => IMAGETYPE_JPEG,
        'jpeg' => IMAGETYPE_JPEG,
        'png' => IMAGETYPE_PNG,
        'gif' => IMAGETYPE_GIF,
        'webp' => IMAGETYPE_WEBP,
      );
      if (!$image_info || $image_info[2] !== $allowed_types[$extension]) {
        watchdog('portfolio', 'Invalid default image: @file', array(
          '@file' => $image_filename,
        ), WATCHDOG_WARNING);
        $success = FALSE;
        continue;
      }

      // Prepare the destination directory.
      $destination_dir = 'public://portfolio-default-content';

      if (!file_prepare_directory($destination_dir, FILE_CREATE_DIRECTORY | FILE_MODIFY_PERMISSIONS)) {
        watchdog('portfolio', 'Could not prepare default content directory: @directory', array(
        '@directory' => $destination_dir,
        ), WATCHDOG_ERROR);

        $success = FALSE;
        continue;
      }

      $destination = $destination_dir . '/' . $image_filename;

      // Reuse an existing managed file if the physical file exists.
      $fid = db_select('file_managed', 'f')
        ->fields('f', array('fid'))
        ->condition('uri', $destination)
        ->execute()
        ->fetchField();

      $image_file = FALSE;
      if ($fid) {
        $existing_file = file_load($fid);
        if ($existing_file && file_exists($existing_file->uri)) {
          $image_file = $existing_file;
        }
      }

      // Restore or register the image if it is missing.
      if (!$image_file) {
        $image_data = file_get_contents($image_source);
        if ($image_data === FALSE) {
          watchdog('portfolio', 'Could not read default image: @file', array(
            '@file' => $image_filename,
          ), WATCHDOG_ERROR);
          $success = FALSE;
          continue;
        }

        // Reuse the existing managed record when restoring its file.
        $image_file = file_save_data($image_data, $destination, FILE_EXISTS_REPLACE);
      }

      if (!$image_file) {
        watchdog('portfolio', 'Could not save or restore default image: @file', array(
          '@file' => $image_filename,
        ), WATCHDOG_ERROR);
        $success = FALSE;
        continue;
      }

      // Assign the image to the node.
      $node->field_image[LANGUAGE_NONE][0] = array(
        'fid' => $image_file->fid,
        'alt' => isset($item['field_image']['alt']) ? $item['field_image']['alt'] : '',
        'title' => isset($item['field_image']['title']) ? $item['field_image']['title'] : '',
      );
    }

    // Add the taxonomy term reference.
    if ($type == 'post' && !empty($item['field_portfolio_main'])) {
      $tid = db_select('taxonomy_term_data', 't')
        ->fields('t', array('tid'))
        ->condition('t.vocabulary', $data['vocabulary'])
        ->condition('t.name', $item['field_portfolio_main'])
        ->execute()
        ->fetchField();

      if (!$tid) {
        watchdog('portfolio', 'Taxonomy term not found: @name', array('@name' => $item['field_portfolio_main']), WATCHDOG_WARNING);
        $success = FALSE;
        continue;
      }

      $node->field_portfolio_main[LANGUAGE_NONE][0] = array(
        'tid' => $tid,
      );
    }

    // Add checkbox fields.
    if ($type == 'post') {
      $checkbox_fields = array(
        'field_block_home',
        'field_block_project',
        'field_block_article',
      );

      foreach ($checkbox_fields as $field_name) {
        if (isset($item[$field_name])) {
          $node->{$field_name}[LANGUAGE_NONE][0] = array(
            'value' => (int) $item[$field_name],
          );
        }
      }
    }

    // Save the node.
    if (node_save($node) != SAVED_NEW) {
      $success = FALSE;
      continue;
    }

    // Add URL alias.
    $alias = array(
      'source' => 'node/' . $node->nid,
      'alias' => $path,
      'langcode' => LANGUAGE_NONE,
    );

    path_save($alias);

    // Record imported content in configuration.
    $imported[$path] = $node->nid;
    $config->set('content_imported_paths', $imported);
    $config->save();

    $created++;
  }

  if (!$success) {
    return FALSE;
  }

  return $created;
}

function portfolio_install_default_views_blocks() {
  $directory = backdrop_get_path('theme', 'portfolio') . '/settings-content/views';

  if (!is_dir($directory)) {
    watchdog('portfolio', 'Views directory not found: @directory', array(
      '@directory' => $directory,
    ), WATCHDOG_ERROR);
    return FALSE;
  }

  $storage = new ConfigFileStorage($directory);

  $views = array(
    'views.view.block_home_personal',
    'views.view.home_project',
    'views.view.home_article',
  );

  foreach ($views as $config_name) {
    watchdog('portfolio', 'Processing View: @name', array(
     '@name' => $config_name,
    ), WATCHDOG_NOTICE);

    $data = $storage->read($config_name);

    if (!is_array($data)) {
      watchdog('portfolio', 'Could not read Views config: @name', array(
        '@name' => $config_name,
      ), WATCHDOG_ERROR);
      return FALSE;
    }

    if (empty($data['name'])) {
      watchdog('portfolio', 'Invalid Views config data: @name', array(
        '@name' => $config_name,
      ), WATCHDOG_ERROR);
      return FALSE;
    }

    $config = config($config_name);

    if (!$config->isNew()) {
      watchdog('portfolio', 'View already exists, skipping: @name', array(
       '@name' => $config_name,
      ), WATCHDOG_NOTICE);
      continue;
    }

    $config->setData($data);

    if ($config_name == 'views.view.block_home_personal') {
      portfolio_install_default_personal_view_filters($config);
    }

    if ($config_name == 'views.view.home_project') {
      if (!portfolio_install_default_project_view_filters($config, 'Projects')) {
        return FALSE;
      }
    }

    if ($config_name == 'views.view.home_article') {
     if (!portfolio_install_default_project_view_filters($config, 'Articles')) {
       return FALSE;
      }
    }

    module_invoke_all('config_create', $config);
    $config->save();
    watchdog('portfolio', 'View saved: @name', array(
  '@name' => $config_name,
), WATCHDOG_NOTICE);
  }

  return TRUE;
}

function portfolio_install_default_personal_view_filters($config) {

  $filters = array(
    'type' => array(
      'id' => 'type',
      'table' => 'node',
      'field' => 'type',
      'value' => array(
        'post' => 'post',
      ),
    ),
    'field_block_home_value' => array(
      'id' => 'field_block_home_value',
      'table' => 'field_data_field_block_home',
      'field' => 'field_block_home_value',
      'operator' => 'or',
      'value' => array(
        '1' => '1',
      ),
      'exposed' => FALSE,
    ),
  );

  $config->set(
    'display.default.display_options.filters',
    $filters
  );

  return TRUE;
}

function portfolio_install_default_project_view_filters($config, $term_name) {
  // Find the category term.
  $terms = taxonomy_term_load_multiple_by_name($term_name, 'portfolio_main');

  if (count($terms) != 1) {
    watchdog('portfolio', 'Expected one @term term in portfolio_main vocabulary, found @count.', array(
      '@term' => $term_name,
      '@count' => count($terms),
    ), WATCHDOG_ERROR);
    return FALSE;
  }

  $term = reset($terms);
  $filters = $config->get('display.default.display_options.filters');

  if (empty($filters['field_portfolio_main_tid'])) {
    watchdog('portfolio', 'Category filter not found in Views configuration.', array(), WATCHDOG_ERROR);
    return FALSE;
  }

  $filters['field_portfolio_main_tid']['value'] = array(
    $term->tid => (string) $term->tid,
  );

  $config->set('display.default.display_options.filters', $filters);

  return TRUE;
}

/**
 * Configure Portfolio layouts and home page blocks.
 */
function portfolio_install_default_home_layout() {
  $layout = layout_load('home');
  $default = layout_load('default');

  if (!$layout || !$default) {
    return FALSE;
  }

  // Required blocks: plugin => region.
  $blocks = array(
    'system:header' => 'header',
    'views:block_home_personal-block' => 'top',
    'system:page_components:messages' => 'top',
    'views:home_project-block' => 'content',
    'views:home_article-block' => 'content',
    'system:powered-by' => 'footer',
  );

  // Number of items for Views blocks.
  $views = array(
    'views:block_home_personal-block' => 1,
    'views:home_project-block' => 5,
    'views:home_article-block' => 6,
  );

  // Find existing blocks and remove unwanted ones.
  $existing = array();

  foreach ($layout->content as $uuid => $block) {
    if (!isset($blocks[$block->plugin]) || isset($existing[$block->plugin])) {
      $layout->removeBlock($uuid);
    }
    else {
      $existing[$block->plugin] = $block;
    }
  }

  // Add missing blocks and configure their positions.
  foreach ($blocks as $plugin => $region) {
    if (isset($existing[$plugin])) {
      $block = $existing[$plugin];
      $layout->setBlockPosition($block->uuid, $region);
    }
    else {
      list($module, $delta) = explode(':', $plugin, 2);

      $block = $layout->addBlock($module, $delta, $region);
    }

    // Configure Views blocks.
    if (isset($views[$plugin])) {
      $block->settings['title_display'] = 'none';
      $block->settings['items_per_page'] = (string) $views[$plugin];
      $block->settings['use_pager'] = FALSE;
      $block->settings['more_link'] = FALSE;
    }

    // The main menu has its own block.
    if ($plugin === 'system:header') {
      $block->settings['block_settings']['menu'] = '';
    }
  }

  // Hide the default welcome title.
  $layout->settings['title'] = '';
  $layout->settings['title_display'] = 'none';
  $layout->settings['title_block'] = NULL;

  // Save the home layout.
  $layout->save();

  // Configure the default layout and save it once.
  $default->layout_template = 'boxton';
  $remove_plugins = array(
    'system:breadcrumb',
    'system:page_components:title_combo',
  );
  foreach ($default->content as $uuid => $block) {
    if (in_array($block->plugin, $remove_plugins, TRUE)) {
      $default->removeBlock($uuid);
    }
  }
  $default->settings['title_display'] = 'none';
  $default->save();

  return TRUE;
}
