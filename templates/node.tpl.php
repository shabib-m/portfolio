<?php
/**
 * @file
 * Default theme implementation to display a node.
 *
 * Available variables:
 * - $title: the (sanitized) title of the node.
 * - $content: An array of node items. Use render($content) to print them all,
 *   or print a subset such as render($content['field_example']). Use
 *   hide($content['field_example']) to temporarily suppress the printing of a
 *   given element.
 * - $user_picture: The node author's picture from user-picture.tpl.php.
 * - $date: Formatted creation date. Preprocess functions can reformat it by
 *   calling format_date() with the desired parameters on the $created variable.
 * - $name: Themed username of node author output from theme_username().
 * - $node_url: Direct URL of the current node.
 * - $display_submitted: Whether submission information should be displayed.
 * - $submitted: Submission information created from $name and $date during
 *   template_preprocess_node().
 * - $classes: Array of classes that can be used to style contextually through
 *   CSS. The default values can be one or more of the following:
 *   - node: The current template type; for example, "theme hook".
 *   - node-[type]: The current node type. For example, if the node is a
 *     "Post" it would result in "node-post". Note that the machine
 *     name will often be in a short form of the human readable label.
 *   - view-mode-[view_mode]: The View Mode of the node e.g. teaser or full.
 *   - preview: Nodes in preview mode.
 *   The following are controlled through the node publishing options.
 *   - promoted: Nodes that are promoted.
 *   - sticky: Nodes ordered above other non-sticky nodes in teaser
 *     listings.
 *   - unpublished: Unpublished nodes visible only to administrators.
 * - $attributes: Array of additional HTML attributes that should be added to
 *   the wrapper element. Flatten with backdrop_attributes().
 * - $title_prefix (array): An array containing additional output populated by
 *   modules, intended to be displayed in front of the main title tag that
 *   appears in the template.
 * - $title_suffix (array): An array containing additional output populated by
 *   modules, intended to be displayed after the main title tag that appears in
 *   the template.
 *
 * Other variables:
 * - $node: Full node entity. Contains data that may not be safe.
 * - $type: Node type; for example, post, page, blog, etc.
 * - $uid: User ID of the node author.
 * - $created: Time the node was published formatted in Unix timestamp.
 * - $zebra: Outputs either "even" or "odd". Useful for zebra striping in
 *   teaser listings.
 * - $id: Position of the node. Increments each time it's output.
 * - $comment_count: Number of comments attached to the node.
 * - $comments: The comment-related elements for the node. Contains two values
 *   that are both arrays that need to be rendered through a render() call:
 *   - $comments['comments']: Comments for this node.
 *   - $comments['comment_form']: Form for adding a new comment.
 * - $comment_display_mode: Whether comments are threaded or not, one of the
 *   following PHP constants:
 *   - COMMENT_MODE_FLAT
 *   - COMMENT_MODE_THREADED
 *
 * Node status variables:
 * - $view_mode: Display mode, e.g. 'full', or 'teaser'.
 * - $teaser: Flag for the teaser state (shortcut for $view_mode == 'teaser').
 * - $page: Flag for the full page state.
 * - $promote: Flag for home page promotion state.
 * - $sticky: Flags for sticky post setting.
 * - $status: Flag for published status.
 * - $comment: State of comment settings for the node.
 * - $readmore: Flags true if the teaser content of the node cannot hold the
 *   main body content.
 * - $is_front: Flags true when presented in the home page.
 * - $logged_in: Flags true when the current user is a logged-in member.
 * - $is_admin: Flags true when the current user is an administrator.
 *
 * Field variables: for each field instance attached to the node a corresponding
 * variable is defined; for example, $node->body becomes $body. When needing to
 * access a field's raw values, developers/themers are strongly encouraged to
 * use these variables. Otherwise they will have to explicitly specify the
 * desired field language; for example, $node->body['en'], thus overriding any
 * language negotiation rule that was previously applied.
 *
 * @see template_preprocess()
 * @see template_preprocess_node()
 *
 * @ingroup themeable
 */
?>
<?php
/**
 * @file
 * Default theme implementation to display a node.
 *
 * - $node: The node object with all its data.
 * - $title: The sanitized title of the node.
 * - $content: An array of node items (body, fields, links, etc.).
 */
?>


<div class="max-w-4xl mx-auto px-4 py-12">
  <article id="node-<?php print $node->nid; ?>" class="<?php print implode(' ', $classes); ?> clearfix" <?php print backdrop_attributes($attributes); ?>>

    <!-- Main glass-style content card -->
    <div class="bg-[#091b24]/60 backdrop-blur-md rounded-3xl border border-slate-800/80 p-6 md:p-10 shadow-2xl shadow-sky-500/5">
      
      <!-- Content title -->
      <?php if (!$page): ?>
        <h2<?php printbackdrop_attributes($attributes); ?>>
          <a href="<?php print $node_url; ?>" rel="bookmark" class="text-slate-100 hover:text-sky-400 transition-colors"><?php print $title; ?></a>
        </h2>
      <?php else: ?>
        <h1 class="text-2xl md:text-4xl font-extrabold text-slate-100 mb-4 tracking-tight leading-tight">
          <?php print $title; ?>
        </h1>
      <?php endif; ?>

<!-- Local task tabs (view, edit, etc.) -->
      <div class="content-tabs mb-6 pb-4 border-b border-slate-800/60">
        <div class="inline-flex items-center bg-slate-900/80 p-1.5 rounded-xl border border-slate-800 tabs">
          <?php 
            // Load primary local tasks and render them using the theme system
            $primary_tasks = menu_primary_local_tasks();
            if (!empty($primary_tasks)) {
              print theme('menu_local_tasks', array('primary' => $primary_tasks));
            }
          ?>
        </div>
      </div>


      <!-- Author and publication date -->
      <?php if ($display_submitted): ?>
        <div class="flex items-center gap-3 text-l text-slate-400 mb-6 pb-4 border-b border-slate-800/60">
          <span>By <strong class="text-slate-300"><?php print $name; ?></strong></span>
          <span>•</span>
          <span><?php print format_date($node->created, 'custom', 'd M Y'); ?></span>
          <span>•</span>
          <strong><?php print render($content['field_portfolio_main']); ?></strong>
        </div>
      <?php endif; ?>

      <?php if (isset($content['field_image'])): ?>
        <div class="my-6 overflow-hidden rounded-2xl border border-slate-800/80 bg-slate-900/50 shadow-lg shadow-sky-500/5">
          <?php print render($content['field_image']); ?>
        </div>
      <?php endif; ?>

      <!-- Node content (image and body) -->
      <div class="content text-slate-300 leading-relaxed space-y-6 ">
        <?php
          hide($content['comments']);
          hide($content['links']);
          print render($content);
        ?>
      </div>

    </div>

    <!-- Glass-style comments section -->
    <?php if ($comments): ?>
      <div class="mt-8 bg-[#091b24]/40 text-slate-300 backdrop-blur-md rounded-3xl border border-slate-800/80 p-6 md:p-8 space-y-6">
        <section class="comments space-y-6" id="comments">
          
          <?php if ($comments['comments']): ?>
            <div class="comments-list">
              <h2 class="text-xl font-bold text-slate-100 mb-4"><?php print t('Comments'); ?></h2>
              <?php print render($comments['comments']); ?>
            </div>
          <?php endif; ?>

          <?php if ($comments['comment_form']): ?>
            <div class="comment-form-wrapper pt-6 border-t border-slate-800/60">
              <h2 class="text-xl font-bold text-slate-100 mb-4"><?php print t('Add comment'); ?></h2>
              <?php print render($comments['comment_form']); ?>
            </div>
          <?php endif; ?>

        </section>
      </div>
    <?php endif; ?>

  </article>
</div>