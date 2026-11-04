<?php
/**
 * @file
 * Custom view template to display all the fields in a grid card layout for home_project.
 *
 * - $view: The view in use.
 * - $fields: an array of $field objects. Each one contains:
 *   - $fields['field_name']->content: The output of the field.
 *   - $fields['field_name']->raw: The raw data for the field.
 * - $row: The raw result object from the query.
 */
?>

<!-- Single project card in the grid -->
<article class="group relative bg-[#091b24]/60 backdrop-blur-md rounded-2xl border border-slate-800/80 hover:border-sky-500/50 active:border-sky-500/50 transition-all duration-300 hover:-translate-y-1 active:translate-y-0 hover:shadow-xl hover:shadow-sky-500/10 flex flex-col overflow-hidden">
  
  <!-- Image container with hover and active effects -->
  <div class="relative aspect-video w-full overflow-hidden bg-slate-900">
    <?php if (!empty($fields['field_image'])): ?>
      <?php print $fields['field_image']->content; ?>
    <?php endif; ?>
    
    <!-- Subtle gradient overlay over the image -->
    <div class="absolute inset-0 bg-gradient-to-t from-[#091b24] via-transparent to-transparent opacity-80 pointer-events-none"></div>
    
    <!-- Category badge (if the category field exists) -->
    <?php if (!empty($fields['field_tags'])): ?>
      <div class="absolute top-3 right-3 z-10">
        <?php print $fields['field_tags']->content; ?>
      </div>
    <?php endif; ?>
  </div>

  <!-- Card content -->
  <div class="p-5 flex-1 flex flex-col justify-between">
    <div>
      <!-- Content title -->
      <?php if (!empty($fields['title'])): ?>
      <h3 class="text-lg font-bold text-slate-100 group-hover:text-sky-400 group-active:text-sky-400 transition-colors line-clamp-1">
      <?php print $fields['title']->content; ?>
      </h3>
      <?php endif; ?>   
      
      <!-- Project summary -->
      <div class="mt-2 text-sm text-slate-400 line-clamp-2 leading-relaxed">
        <?php print !empty($fields['body']) ? $fields['body']->content : ''; ?>
      </div>
    </div>

    <!-- Details link at the bottom -->
    <div class="mt-5 pt-4 border-t border-slate-800/60 flex items-center justify-between text-xs text-slate-400">
      <span class="inline-flex items-center gap-1.5 text-sky-400 group-hover:underline group-active:underline font-medium">
        Explore Project
        <svg class="w-3.5 h-3.5 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
      </span>
      
      <!-- Full-card link for easier interaction -->
      <?php if (!empty($row->nid)): ?>
        <a href="<?php print url('node/' . $row->nid); ?>" class="absolute inset-0 z-20" aria-label="View Details"><span class="sr-only">View Details</span></a>
      <?php endif; ?>
    </div>
  </div>

</article>