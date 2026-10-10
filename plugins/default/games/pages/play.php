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

$game_id = isset($params['game_id']) ? trim($params['game_id']) : '';
$cache_file = ossn_get_userdata("gamepix_cache.json");

$game = null;

if (file_exists($cache_file)) {
    $all_games = json_decode(file_get_contents($cache_file), true);
    if (!empty($all_games) && is_array($all_games)) {
        foreach ($all_games as $g) {
            if ((string)$g['id'] === (string)$game_id) {
                $game = $g;
                break;
            }
        }
    }
}

if (!$game) {
	echo '<div class="alert alert-danger my-4">' . ossn_print('games:not_found') . ' <a href="' . ossn_site_url('play/all') . '">' . ossn_print('games:back_to_games') . '</a></div>';    return;
}
?>

<div class="game-play-container">
    <div class="game-play-header">
        <a href="<?php echo ossn_site_url('play/all'); ?>" class="btn-game-back">
            <i class="fa fa-arrow-left"></i> <?php echo ossn_print('games:back_to_games'); ?>
        </a>
        <h2 class="game-play-title"><?php echo htmlspecialchars($game['title']); ?></h2>
    </div>

    <div class="game-iframe-stage">
        <div class="game-iframe-wrapper">
            <iframe 
                src="<?php echo $game['embed_url']; ?>" 
                id="game-iframe" 
                allowfullscreen="true" 
                msallowfullscreen="true" 
                mozallowfullscreen="true" 
                webkitallowfullscreen="true" 
                allow="autoplay; payment; fullscreen; microphone; clipboard-read; clipboard-write; accelerometer; gyroscope; magnetometer; gamepad"
                frameborder="0" 
                scrolling="no">
            </iframe>
        </div>
    </div>

    <?php if (!empty($game['description'])): ?>
        <div class="game-details-card">
            <h4>About <?php echo htmlspecialchars($game['title']); ?></h4>
            <p><?php echo nl2br(htmlspecialchars($game['description'])); ?></p>
        </div>
    <?php endif; ?>
</div>