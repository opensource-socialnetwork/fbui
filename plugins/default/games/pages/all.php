<?php
/**
 * Open Source Social Network
 *
 * @package   Open Source Social Network
 * @author    OSSN Core Team <info@openteknik.com>
 * @copyright (C) OPENTEKNIK LLC, COMMERCIAL LICENSE
 * @license   OPENTEKNIK LLC, COMMERCIAL LICENSE https://www.openteknik.com/license/commercial-license-v1
 * @link      http://www.opensource-socialnetwork.org/licence
 */

if (!ossn_isLoggedin()) {
    return;
}

// 1. Setup local 1-hour file cache
$cache_file = ossn_get_userdata("gamepix_cache.json");
$cache_time = 3600; // 1 hour

$all_games = array();

if (file_exists($cache_file) && (time() - filemtime($cache_file) < $cache_time)) {
    $cache_data = file_get_contents($cache_file);
    $all_games = json_decode($cache_data, true);
} else {
    $api_url = "https://games.gamepix.com/games?pagination=100&page=1";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Accept: application/json, text/plain, */*',
        'Accept-Language: en-US,en;q=0.9',
        'Referer: https://games.gamepix.com/'
    ));

    $response = curl_exec($ch);
    curl_close($ch);

    if ($response) {
        $res_data = json_decode($response, true);
        if (!empty($res_data['data']) && is_array($res_data['data'])) {
            foreach ($res_data['data'] as $item) {
                if (empty($item['id']) || empty($item['title']) || empty($item['url'])) {
                    continue;
                }

                $all_games[] = array(
                    'id'          => $item['id'],
                    'title'       => $item['title'],
                    'category'    => !empty($item['category']) ? ucfirst(strtolower($item['category'])) : 'Arcade',
                    'thumbnail'   => !empty($item['thumbnailUrl']) ? $item['thumbnailUrl'] : $item['bannerUrl'],
                    'embed_url'   => $item['url'],
                    'description' => !empty($item['description']) ? $item['description'] : ''
                );
            }

            if (!empty($all_games)) {
                file_put_contents($cache_file, json_encode($all_games));
            }
        }
    }

    if (empty($all_games) && file_exists($cache_file)) {
        $all_games = json_decode(file_get_contents($cache_file), true);
    }
}

// 2. Extract unique categories for filter pills
$categories = array('All');
if (!empty($all_games) && is_array($all_games)) {
    foreach ($all_games as $g) {
        if (!empty($g['category']) && !in_array($g['category'], $categories)) {
            $categories[] = $g['category'];
        }
    }
}
sort($categories);

// 3. Read parameters safely using OSSN input()
$selected_category = input('category', '', 'All');
$search_query      = input('q', '');

// New flag to detect if the AJAX request came specifically from the games filter
$is_games_ajax     = input('ossn_ajax_games', ''); 

if (empty($selected_category)) {
    $selected_category = 'All';
}

$display_games = $all_games;

// Filter by category
if ($selected_category !== 'All') {
    $display_games = array_filter($display_games, function($g) use ($selected_category) {
        return strcasecmp($g['category'], $selected_category) === 0;
    });
}

// Filter by search query
if (!empty($search_query)) {
    $display_games = array_filter($display_games, function($g) use ($search_query) {
        return stripos($g['title'], $search_query) !== false || stripos($g['category'], $search_query) !== false;
    });
}

$display_games = array_slice($display_games, 0, 36);

// -------------------------------------------------------------
// IF AJAX REQUEST: Check for 'ossn_ajax_games' flag so it doesn't conflict with Topbar PJAX
// -------------------------------------------------------------
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest' && $is_games_ajax == '1') {
    if (!empty($display_games)) {
        foreach ($display_games as $game) {
            ?>
            <div class="game-card">
                <a href="<?php echo ossn_site_url("play/game/{$game['id']}"); ?>">
                    <div class="game-thumbnail-wrapper">
                        <img src="<?php echo $game['thumbnail']; ?>" 
                             alt="<?php echo htmlspecialchars($game['title']); ?>" 
                             loading="lazy" />
                        <div class="game-play-overlay">
                            <i class="fa fa-play-circle"></i>
                        </div>
                    </div>
                    <div class="game-info">
                        <div class="game-title" title="<?php echo htmlspecialchars($game['title']); ?>">
                            <?php echo htmlspecialchars($game['title']); ?>
                        </div>
                        <span class="game-category"><?php echo htmlspecialchars($game['category']); ?></span>
                    </div>
                </a>
            </div>
            <?php
        }
    } else {
        echo '<div class="alert alert-info text-center w-100 my-3">' . ossn_print('games:no_results') . '</div>';
    }
    exit;
}
?>

<!-- -------------------------------------------------------------
     MAIN FULL PAGE VIEW
------------------------------------------------------------- -->
<div class="games-container">
    
    <!-- Top Header Bar with Live Search -->
    <div class="games-header-bar mb-3">
        <h3 class="m-0"><?php echo ossn_print('games:title'); ?></h3>
        
        <div class="games-search-box">
            <i class="fa fa-search search-icon"></i>
            <input type="text" 
                   id="ossn-game-search-input" 
                   class="form-control" 
                   placeholder="<?php echo ossn_print('games:search_placeholder'); ?>" 
                   value="<?php echo htmlspecialchars($search_query); ?>" />
        </div>
    </div>

    <!-- Category Filter Pills Bar -->
    <?php if (!empty($categories)): ?>
        <div class="games-filter-bar mb-4">
            <?php foreach ($categories as $cat): ?>
                <?php 
                    $active_class = (strcasecmp($selected_category, $cat) === 0) ? 'active' : '';
                    
                    // Dynamic translation key logic
                    $cat_key = 'games:category:' . strtolower($cat);
                    $translated_cat = ossn_print($cat_key);
                    
                    if ($translated_cat === $cat_key) {
                        $translated_cat = htmlspecialchars($cat);
                    }

                    if ($cat === 'All') {
                        $translated_cat = ossn_print('games:all_categories');
                    }
                ?>
                <button type="button" 
                        class="game-filter-pill <?php echo $active_class; ?>" 
                        data-category="<?php echo htmlspecialchars($cat); ?>">
                    <?php echo $translated_cat; ?>
                </button>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Dynamic AJAX Grid Container -->
    <div class="games-grid-wrapper">
        <div class="games-loading-overlay d-none" id="games-loader">
            <div class="spinner-border text-primary" role="status"></div>
        </div>

        <div class="games-grid" id="ossn-games-grid">
            <?php if (!empty($display_games)): ?>
                <?php foreach ($display_games as $game): ?>
                    <div class="game-card">
                        <a href="<?php echo ossn_site_url("play/game/{$game['id']}"); ?>">
                            <div class="game-thumbnail-wrapper">
                                <img src="<?php echo $game['thumbnail']; ?>" 
                                     alt="<?php echo htmlspecialchars($game['title']); ?>" 
                                     loading="lazy" />
                                <div class="game-play-overlay">
                                    <i class="fa fa-play-circle"></i>
                                </div>
                            </div>
                            <div class="game-info">
                                <div class="game-title" title="<?php echo htmlspecialchars($game['title']); ?>">
                                    <?php echo htmlspecialchars($game['title']); ?>
                                </div>
                                <span class="game-category"><?php echo htmlspecialchars($game['category']); ?></span>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-info text-center w-100 my-3">
                    <?php echo ossn_print('games:no_results'); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- AJAX JavaScript Reload Script -->
<script>
$(document).ready(function() {
    let currentCategory = '<?php echo addslashes($selected_category); ?>';
    let searchTimeout = null;

    function reloadGamesGrid(category, query) {
        $('#games-loader').removeClass('d-none');
        
        let baseUrl = Ossn.site_url + 'play/all';
        let params = [];
        
        // Pass the flag to bypass PJAX topbar conflict
        params.push('ossn_ajax_games=1');
        
        if (category && category !== 'All') {
            params.push('category=' + encodeURIComponent(category));
        }
        if (query && query.trim() !== '') {
            params.push('q=' + encodeURIComponent(query.trim()));
        }
        
        let ajaxUrl = baseUrl;
        if (params.length > 0) {
            ajaxUrl += '?' + params.join('&');
        }

        // Silent AJAX Request (No pushState)
        $.ajax({
            url: ajaxUrl,
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(htmlResponse) {
                $('#ossn-games-grid').html(htmlResponse);
                $('#games-loader').addClass('d-none');
            },
            error: function() {
                $('#games-loader').addClass('d-none');
            }
        });
    }

    // Category Pill Click Handler
    $(document).on('click', '.game-filter-pill', function(e) {
        e.preventDefault();
        $('.game-filter-pill').removeClass('active');
        $(this).addClass('active');

        currentCategory = $(this).attr('data-category');
        let searchQuery = $('#ossn-game-search-input').val();

        reloadGamesGrid(currentCategory, searchQuery);
    });

    // Real-time Search Input Handler
    $(document).on('input', '#ossn-game-search-input', function() {
        clearTimeout(searchTimeout);
        let searchQuery = $(this).val();

        searchTimeout = setTimeout(function() {
            reloadGamesGrid(currentCategory, searchQuery);
        }, 300);
    });
});
</script>