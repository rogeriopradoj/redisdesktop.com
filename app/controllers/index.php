<?php
/**
 * todo: description
 *
 * @author Igor Malinovskiy <glide.name>
 * @file index.php
 * @date: 27.11.13
 */

/**
 * @param array $issues
 * @return array
 */
function extractUsersFromGithubIssues(Array $issues)
{
    $users = array();
    foreach ($issues as $issue) {
        $users[] = $issue['user'];
    }

    return $users;
}

function getAllContributors() {

    $client = new Github\Client(
        new Github\HttpClient\CachedHttpClient(array('cache_dir' => APP_PATH . '/cache/github-api-cache'))
    );

    /**
     * get users that contribute to code and create issues
     */
    $contributors = $client->api('repo')->contributors('uglide', 'RedisDesktopManager');
    $contributorsToSite = $client->api('repo')->contributors('RedisDesktop', 'redisdesktop.com');

    $issuesApi = $client->api('issue');
    $paginator  = new Github\ResultPager($client);

    $closedIssuesCreators = extractUsersFromGithubIssues(
        $paginator->fetchAll($issuesApi, 'all', array('uglide', 'RedisDesktopManager', array('state' => 'closed')))
    );

    $openedIssuesCreators = extractUsersFromGithubIssues(
        $paginator->fetchAll($issuesApi, 'all', array('uglide', 'RedisDesktopManager', array('state' => 'open')))
    );

    return array_merge(
            $contributors,
            $contributorsToSite,
            $closedIssuesCreators,
            $openedIssuesCreators
        );
}

return function () use ($rdmData, $app) {

    $title = "Redis Desktop Manager - Redis GUI management tool for Windows, Mac OS X, Ubuntu and Debian.";
    $description = "Cross-platform redis desktop manager - desktop management GUI for mac os x, windows, debian and ubuntu.";

    if ($app['cache']->contains('allContributors')) {
        $allContributors = $app['cache']->fetch('allContributors');
    } else {

	try {	

        	$allContributors = getAllContributors();
	        $app['cache']->save('allContributors', $allContributors, 100000);

	} catch (Exception $e) {
 		$allContributors = array();
	}
    }

    $content = require APP_PATH . '/views/main.phtml';
    $layout = require APP_PATH . '/views/layout.phtml';

    return $layout;
};