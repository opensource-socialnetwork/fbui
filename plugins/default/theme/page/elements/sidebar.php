<?php
if(!ossn_isLoggedin()){
	return;
}
$user = ossn_loggedin_user();
$site_url = ossn_site_url();
?>
<div class="fbui-sidebar-wrapper">
	<div class="fbui-static-sidebar">
		<div class="fbui-static-sidebar-content">
			<ul class="fbui-sidebar-list">
				
				<!-- 1. Profile -->
				<li>
					<a href="<?php echo $user->profileURL(); ?>">
						<div class="fbui-sidebar-avatar">
							<img src="<?php echo $user->iconURL()->small; ?>" alt="<?php echo $user->fullname; ?>" />
						</div>
						<span class="fbui-sidebar-label"><?php echo $user->fullname; ?></span>
					</a>
				</li>

				<li>
					<a href="<?php echo $site_url; ?>home">
						<div class="fbui-sidebar-icon icon-newsfeed">
							<i class="fa-solid fa-newspaper"></i>
						</div>
						<span class="fbui-sidebar-label"><?php echo ossn_print('news:feed');?></span>
					</a>
				</li>
				<li>
					<a href="<?php echo $site_url; ?>friends">
						<div class="fbui-sidebar-icon icon-friends">
							<i class="fa-solid fa-user-group"></i>
						</div>
						<span class="fbui-sidebar-label"><?php echo ossn_print('friends');?></span>
					</a>
				</li>

				<?php if(com_is_active('Videos')){ ?>
				<li>
					<a href="<?php echo $site_url; ?>video/watch">
						<div class="fbui-sidebar-icon icon-watch">
							<i class="fa-brands fa-youtube"></i>
						</div>
						<span class="fbui-sidebar-label"><?php echo ossn_print('video:watch');?></span>
					</a>
				</li>
				<?php } ?>
				<?php if(com_is_active('OssnGroups')){ ?>
				<li>
					<a href="<?php echo $site_url; ?>search?type=groups&q=">
						<div class="fbui-sidebar-icon icon-groups">
							<i class="fa fa-users-rectangle"></i>
						</div>
						<span class="fbui-sidebar-label"><?php echo ossn_print('groups');?></span>
					</a>
				</li>
                <?php } ?>

				<?php if(com_is_active('BusinessPage')){ ?>
				<li>
					<a href="<?php echo $site_url; ?>page/all">
						<div class="fbui-sidebar-icon icon-pages">
							<i class="fa fa-flag"></i>
						</div>
						<span class="fbui-sidebar-label"><?php echo ossn_print('fbui:bpages');?></span>
					</a>
				</li>
				<?php } ?>
				<?php if(com_is_active('Events')){ ?>
				<li>
					<a href="<?php echo $site_url; ?>events">
						<div class="fbui-sidebar-icon icon-events">
							<i class="fa fa-calendar-alt"></i>
						</div>
						<span class="fbui-sidebar-label"><?php echo ossn_print('events');?></span>
					</a>
				</li>
				<?php } ?>
				<?php if(com_is_active('MarketPlace')){ ?>
				<li>
					<a href="<?php echo $site_url; ?>marketplace">
						<div class="fbui-sidebar-icon icon-marketplace">
							<i class="fa fa-store"></i>
						</div>
						<span class="fbui-sidebar-label"><?php echo ossn_print('marketplace');?></span>
					</a>
				</li>
				<?php } ?>
				<?php if(com_is_active('Bookmarks')){ ?>
				<li>
					<a href="<?php echo $site_url; ?>saved">
						<div class="fbui-sidebar-icon icon-saved">
							<i class="fa fa-bookmark"></i>
						</div>
						<span class="fbui-sidebar-label"><?php echo ossn_print('bookmark:saved');?></span>
					</a>
				</li>
				<?php } ?>

				<?php if(com_is_active('Blog')){ ?>
				<li>
					<a href="<?php echo $site_url; ?>blog/all_blogs_by_date">
						<div class="fbui-sidebar-icon icon-blogs">
							<i class="fa fa-newspaper"></i>
						</div>
						<span class="fbui-sidebar-label"><?php echo ossn_print('com:blog:blog:all');?></span>
					</a>
				</li>
				<?php } ?>
				<?php if(com_is_active('OssnMessages')){ ?>
				<li>
					<a href="<?php echo $site_url; ?>messages/all">
						<div class="fbui-sidebar-icon icon-messenger">
							<i class="fa-brands fa-facebook-messenger"></i>
						</div>
						<span class="fbui-sidebar-label"><?php echo ossn_print('fbui:messenger');?></span>
					</a>
				</li>
				<?php } ?>                
				<?php if(com_is_active('Polls')){ ?>
				<li>
					<a href="<?php echo $site_url; ?>polls/list/all">
						<div class="fbui-sidebar-icon icon-polls">
							<i class="fa fa-square-poll-vertical"></i>
						</div>
						<span class="fbui-sidebar-label"><?php echo ossn_print('polls');?></span>
					</a>
				</li>
				<?php } ?>
				<?php if(com_is_active('MP3')){ ?>
				<li>
					<a href="<?php echo $site_url; ?>mp3/all">
						<div class="fbui-sidebar-icon icon-mp3">
							<i class="fa fa-music"></i>
						</div>
						<span class="fbui-sidebar-label"><?php echo ossn_print('mp3file');?></span>
					</a>
				</li>
				<?php } ?>
				<?php if(com_is_active('Monetization')){ ?>
				<li>
					<a href="<?php echo $site_url; ?>campaigns/dashboard">
						<div class="fbui-sidebar-icon icon-adsmanager">
							<i class="fa-solid fa-chart-simple"></i>
						</div>
						<span class="fbui-sidebar-label"><?php echo ossn_print('fbui:adsmanager');?></span>
					</a>
				</li>
				<?php } ?>                
				<li>
					<a href="<?php echo $site_url; ?>play/all">
						<div class="fbui-sidebar-icon icon-games">
							<i class="fas fa-gamepad"></i>
						</div>
						<span class="fbui-sidebar-label"><?php echo ossn_print('games');?></span>
					</a>
				</li>
				<li>
					<a href="<?php echo $site_url; ?>search?q=">
						<div class="fbui-sidebar-icon icon-members">
							<i class="fa fa-address-book"></i>
						</div>
						<span class="fbui-sidebar-label"><?php echo ossn_print('fbui:members');?></span>
					</a>
				</li>

				<!-- 15. See More Toggle -->
				<li class="fbui-sidebar-see-more d-none">
					<a href="javascript:void(0);">
						<div class="fbui-sidebar-icon icon-see-more">
							<i class="fa fa-chevron-down"></i>
						</div>
						<span class="fbui-sidebar-label">See more</span>
					</a>
				</li>

			</ul>
		</div>
	</div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function() {
    function syncSidebarWidth() {
        const col = document.querySelector('.newsfeed-col-left');
        const sidebar = document.querySelector('.fbui-sidebar-wrapper');
        if (col && sidebar) {
            // Copy exact computed width of the parent grid column
            sidebar.style.width = col.getBoundingClientRect().width + 'px';
        }
    }

    // Run on load and whenever window resizes
    syncSidebarWidth();
    window.addEventListener('resize', syncSidebarWidth);
});
</script>