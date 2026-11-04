<?php
/**
 * @file
 * Template for the personal block view fields.
 */

$image_url = '';

if (!empty($fields['field_image']->content)) {
  $content = $fields['field_image']->content;

  if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $content, $matches)) {
    $image_url = $matches[1];
  }
}
?>

<section class="flex-grow flex items-center justify-center px-4 pt-2 pb-12 relative z-10">
  <div class="max-w-3xl mx-auto text-center flex flex-col items-center">

    <!-- Dynamic profile image -->
    <div class="mb-6 relative flex justify-center items-center">
      <div class="absolute w-36 h-36 sm:w-56 sm:h-56 rounded-full bg-gradient-to-r from-sky-400/30 to-blue-600/30 blur-xl -z-10"></div>
      <div class="w-32 h-32 sm:w-52 sm:h-52 rounded-full overflow-hidden border border-white/20 shadow-2xl bg-[#091b24] flex items-center justify-center relative z-10">
        <?php if ($image_url): ?>
          <img src="<?php print check_plain($image_url); ?>" class="w-full h-full object-cover block" alt="">
        <?php else: ?>
          <div class="text-sky-400 font-bold text-2xl">
            <?php print mb_substr($node->title, 0, 1); ?>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Dynamic body field -->
    <div class="w-full text-white mb-8">
      <?php 
        if (!empty($fields['body']->content)) {
            print $fields['body']->content;
        }
      ?>
    </div>

    <!-- Call-to-action buttons -->
    <div class="flex flex-col sm:flex-row items-center gap-4 w-full justify-center">
      <a href="#contact" class="w-full sm:w-auto px-6 py-2.5 rounded-full bg-white text-[#051218] font-medium hover:bg-gray-100 transition shadow-md text-sm">
        Let's talk
      </a>
      <a href="#works" class="w-full sm:w-auto px-6 py-2.5 rounded-full bg-[#091b24] border border-white/10 text-white font-medium hover:bg-white/5 transition flex items-center justify-center gap-2 text-sm">
        View Works
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M17 7H7M17 7v10"></path></svg>
      </a>
    </div>

  </div>
</section>