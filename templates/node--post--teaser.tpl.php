<article id="node-<?php print $node->nid; ?>" class="h-full bg-[#091b24]/50 backdrop-blur-md rounded-3xl border border-slate-800/80 hover:border-sky-500/50 p-5 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-sky-500/10 flex flex-col justify-between group">
  
  <div>
    <!-- Content image (top, with rounded corners) -->
    <?php if (isset($content['field_image'])): ?>
      <div class="overflow-hidden rounded-2xl mb-4 bg-slate-900/50 aspect-video">
        <?php print render($content['field_image']); ?>
      </div>
    <?php endif; ?>

    <!-- Content title -->
    <h3 class="text-lg font-bold text-slate-100 group-hover:text-sky-400 transition-colors mb-2 line-clamp-1">
      <a href="<?php print $node_url; ?>"><?php print $title; ?></a>
    </h3>

    <!-- Content summary -->
    <div class="text-sm text-slate-400 leading-relaxed line-clamp-3 mb-4">
      <?php 
        // Hide unwanted fields and render the content summary only
        hide($content['comments']);
        hide($content['links']);
        hide($content['field_image']);
        print render($content); 
      ?>
    </div>
  </div>

  <!-- Card footer: date and details link -->
  <div class="pt-3 border-t border-slate-800/60 flex items-center justify-between text-xs text-slate-400">
    <span><?php print format_date($node->created, 'custom', 'd M Y'); ?></span>
    <a href="<?php print $node_url; ?>" class="inline-flex items-center gap-1 text-sky-400 hover:text-sky-300 font-medium transition-colors">
      View Details 
      <span class="rtl:rotate-180">&rarr;</span>
    </a>
  </div>

</article>