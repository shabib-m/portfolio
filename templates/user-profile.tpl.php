<?php
/**
 * @file
 * Template for rendering the user profile page.
 *
 * Available variables:
 * - $user_profile: An array of profile items. Use render($user_profile) to print them all,
 *   or print a subset such as render($user_profile['user_picture']).
 * - $account: The user object for the user whose profile is being viewed.
 */


  // 1. Safely initialize the user account at the top of the template
  $account = isset($elements['#account']) ? $elements['#account'] : NULL;
?>
<div class="user-profile-wrapper max-w-4xl mx-auto my-8 px-4" >
  <div class="bg-[#091b24]/60 backdrop-blur-md rounded-3xl border border-slate-800/80 p-6 md:p-8 shadow-2xl shadow-sky-500/5 text-slate-300">
    
    <!-- Header Area: Avatar & Account Info -->
    <div class="profile-header flex flex-col sm:flex-row items-center gap-6 pb-6 mb-6 border-b border-slate-800/60 text-center">
      
      <?php if (!empty($user_profile['user_picture'])): ?>
        <div class="profile-avatar overflow-hidden rounded-2xl border border-slate-700/60 shadow-md">
          <?php print render($user_profile['user_picture']); ?>
        </div>
      <?php else: ?>
        <div class="profile-avatar-fallback w-20 h-20 rounded-2xl bg-slate-900/80 border border-slate-700/60 flex items-center justify-center text-sky-400 font-bold text-2xl shadow-inner">
          <?php print mb_substr($account->name, 0, 1); ?>
        </div>
      <?php endif; ?>

      <div class="profile-title">
        <h1 class="text-2xl md:text-3xl font-extrabold text-slate-100 mb-1">
          <?php if (!empty($elements['#account']->name)): ?>
           <?php print check_plain($elements['#account']->name); ?>
          <?php endif; ?>
        </h1>
        <p class="text-xs text-slate-400">
        <!-- 2. Safely display the join date inside the glass-style card -->
        <?php if ($account && !empty($account->created)): ?>
          <div class="text-xs text-slate-400 mt-1" dir="auto">
            <?php print t('Joined on @date', array('@date' => format_date($account->created, 'custom', 'j F Y'))); ?>
          </div>
        <?php endif; ?>
        </p>
      </div>

    </div>

    <!-- Main Content Area: Dynamic Render of all custom fields -->
    <div class="profile-body space-y-6">
      <?php 
        // Hide user_picture as it is rendered above in the header
        hide($user_profile['user_picture']);
        
        // Render all remaining default and custom fields dynamically
        print render($user_profile); 
      ?>
    </div>

  </div>
</div>