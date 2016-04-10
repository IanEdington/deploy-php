<?php

require_once(dirname(__FILE__) . "/functions.php");
$headers = getallheaders();

// check that it's a push event
if ($headers['X-Github-Event'] != "push") {
    header('HTTP/1.0 403 Forbidden');
    die('not a push event');
}

// Load yaml/json into deploy-config object.
$json = file_get_contents("config.json");
$config_file = json_decode($json, true);
$settings = array( 'secrets'=>array() );
$repoDefault = '@defalt-repo@';
$branchDefault = '@default-branch@';

$settings = cascading_settings($config_file, $settings, $repoDefault, $branchDefault);
$default = [
    // Required
    'repository' => '',
    'target_dir' => '',

    // Other Settings
    'branch' => 'master',
    'secret_access_token' => '',
    'git_path' => 'git', // file path to git executable
    'time_limit' => 30, //
    'backup_dir' => '',
    'clean_up' => '',
    'email_on_error' => '',

    // Composer Settings
    'composer' => false, // if this is true composer will be checked as a requirement
    'composer_path' => 'composer', //
    'composer_options' => '',
    'composer_home' => '',

    // rsync is useful for deploying to remote server, among other things.
    // If EXCLUDE is configured properly this works well.
    'rsync' => false, // use rsync or not
    'rsync_path' => 'rsync', // path to rsync executable
    'rsync_staging_dir' => false, // path to local staging directory
    'rsync_exclude' => [], // array of files to exclude from rsync.
    'rsync_version_file' => false, // should it be included?
    'rsync_delete_files' => false, // When files are deleted in rsync_staging_dir should the be deleted from target_dir?
];


//MAKE SURE It's Secure

//Check webhookData hash against all SECRET\_ACCESS\_TOKEN's to findout if matches one of them.
// ref: http://isometriks.com/verify-github-webhooks-with-php

$hubSignature = $headers['X-Hub-Signature'];

// Split signature into algorithm and hash
list($algo, $hash) = explode('=', $hubSignature, 2);

// Test webhookData against all secrets to make sure at least one works
$secrets = $settings['secrets'];
$rawWebhookData = file_get_contents('php://input');
$a_secret_matches = false;

foreach ($secrets as $secret) {
    $webhookDataHash = hash_hmac($algo, $rawWebhookData, $secret);

    if (hash_equals($hash, $webhookDataHash)) {
        $a_secret_matches = true;
    }
}

if ($a_secret_matches) {
    echo "the hash matched".PHP_EOL;
} else {
    header('HTTP/1.0 403 Forbidden');
    die('hash not equivalent');
}


// Find out what repo & branch to use
$webhookData = json_decode($rawWebhookData, true);

$repo = $webhookData['repository']['full_name'];
$branch = explode('/', $webhookData['ref'], 3)[2];

// tease out requirments from settings file
$deployments = [];
$deployments[] = findBranchSettings($settings, $repo, $branch);


//Does the server have everything it needs?

//MAKE SURE IT'S SETUP properly
//get the repo and branch from the webhookData
//check if repo and branch match a deployment config
//yes - keep going
//no - throw an error

//Build up the options
//based on the repo and branch determin which deployments
//will be triggered (possibly more than one. but start with one)

//Once the options are built run through each deployment script echoing output along the way.

// Deploy settings[$repo][$branch]
// Deploy settings[$repo]["%any%"]
// Deploy settings["%any%"]["%any%"]
