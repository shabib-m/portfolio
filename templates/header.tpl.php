<?php
/**
 * @file
 * Display generic site information such as logo, site name, etc., with main menu integrated.
 */
?>
<!-- Unified floating header (site name and navigation) -->
 <div class="flex justify-center">
<header class="fixed top-2 z-50 max-w-3xl" role="banner" aria-label="<?php print t('Site header'); ?>">
  <div class="flex items-center justify-center break-words px-6 py-2 bg-[#051218]/90 text-white backdrop-blur-md border border-white/10 rounded-full shadow-2xl">

    <!-- 1. Site name and logo -->
    <?php if ($site_name || $logo): ?>
      <div class="name-and-slogan flex items-center flex-shrink-0">
        <a href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>" rel="home" class="flex items-center gap-2 group">
          <?php if ($logo): ?>
            <img src="<?php print $logo; ?>" alt="<?php print t('Home'); ?>" class="h-7 w-auto object-contain transition-transform group-hover:scale-105" />
          <?php endif; ?>
          <?php if ($site_name): ?>
            <span class="site-name text-lg font-bold tracking-tight text-white group-hover:text-gray-200 transition">
              <?php print $site_name; ?>
            </span>
          <?php endif; ?>
        </a>
      </div>
    <?php endif; ?>

<!-- Desktop main navigation -->
<nav class="hidden md:flex items-center gap-6">
  <?php 
    $menu_links = menu_navigation_links('main-menu');
    if (!empty($menu_links)): 
      print theme('links', array(
        'links' => $menu_links,
        'attributes' => array(
          'class' => array('flex', 'items-center', 'gap-6', 'text-lg', 'text-white/90', 'list-none', 'm-0', 'p-0')
        ),
      ));
    endif; 
  ?>
</nav>

    <!-- Mobile menu toggle (hamburger) -->
    <div class="flex items-center md:hidden">
      <input type="checkbox" id="mobile-menu-toggle" class="peer hidden">
      <label for="mobile-menu-toggle" class="cursor-pointer p-2 text-white/80 hover:text-white focus:outline-none">
        <svg class="w-6 h-6 transition-transform duration-300 peer-checked:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
      </label>

      <!-- Mobile dropdown menu -->
<div class="absolute top-16 right-0 left-0 bg-[#091b24]/95 backdrop-blur-xl border-b border-white/10 shadow-2xl py-4 px-6 flex flex-col gap-4 transform -translate-y-full opacity-0 pointer-events-none transition-all duration-300 peer-checked:translate-y-0 peer-checked:opacity-100 peer-checked:pointer-events-auto md:hidden">
    <?php 
      if (!empty($menu_links)): 
        print theme('links', array(
          'links' => $menu_links,
          'attributes' => array(
            'class' => array('flex', 'flex-col', 'gap-3', 'list-none', 'm-0', 'p-0')
          ),
        ));
      endif; 
    ?>
  </div>
    </div>
  </div>
  </div>
</header>