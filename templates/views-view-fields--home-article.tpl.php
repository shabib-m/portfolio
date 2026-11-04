<?php
/**
 * @file
 * Custom view template for home_article horizontal card layout (Image on the right).
 */
?>
<article class="group relative bg-[#091b24]/60 backdrop-blur-md rounded-2xl border border-slate-800/80 hover:border-amber-500/60 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-amber-500/10 overflow-hidden flex flex-col sm:flex-row">
  
  <!-- Image container (position follows the page direction) -->
  <div class="relative w-full -pb-4 sm:w-36 sm:min-w-[144px] aspect-video sm:aspect-auto overflow-hidden bg-slate-900 order-2 sm:order-1">
    <?php if (!empty($fields['field_image'])): ?>
      <?php 
        $image_output = str_ireplace('<img', '<img class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"', $fields['field_image']->content);
        print $image_output;
      ?>
    <?php endif; ?>
  </div>
  <!-- Text content container -->
  <div class="pt-4 pr-2 flex-1 flex flex-col justify-between order-2 sm:order-1">
    <div>
      <!-- Article title -->
      <?php if (!empty($fields['title'])): ?>
        <h3 class="text-base font-bold text-slate-100 group-hover:text-sky-400 transition-colors line-clamp-1">
          <?php print $fields['title']->content; ?>
        </h3>
      <?php endif; ?>
      
      <!-- Article summary -->
      <?php if (!empty($fields['body'])): ?>
        <div class="mt-1.5 text-xs text-slate-400 line-clamp-2 leading-relaxed">
          <?php print $fields['body']->content; ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- Read article link at the bottom -->
    <div class="mt-1 pt-2.5 pb-1 border-t border-slate-800/60 flex items-center justify-between text-xs text-slate-400">
      <span class="inline-flex items-center gap-1 text-sky-400 group-hover:underline font-medium">
        Read Article
        <svg class="w-3 h-3 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
      </span>
    </div>
  </div>

  <!-- Full-card clickable link -->
  <?php if (!empty($row->nid)): ?>
    <a href="<?php print url('node/' . $row->nid); ?>" class="absolute inset-0 z-20" aria-label="Read Article">
      <span class="sr-only">Read Article</span>
    </a>
  <?php endif; ?>

</article>